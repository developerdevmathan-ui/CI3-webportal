<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller
{
    protected $stripe_secret;
    protected $stripe_webhook_secret;
    protected $stripe_currency;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');
        $this->load->model('Payment_model');
        $this->load->model('Invoice_model');
        $this->load->model('Receipt_model');

        $this->stripe_secret = $this->config->item('stripe_secret');
        $this->stripe_webhook_secret = $this->config->item('stripe_webhook_secret');
        $this->stripe_currency = $this->config->item('stripe_currency') ?: 'usd';
    }

    public function create_checkout($product_id)
    {
        $this->require_role(self::ROLE_USER);

        if ($this->input->method(TRUE) !== 'POST')
        {
            show_error('Invalid request method.', 405);
        }

        $product = $this->Product_model->get_by_id($product_id);

        if (empty($product))
        {
            $this->session->set_flashdata('error', 'The selected product could not be found.');
            redirect('products');
        }

        if ((int) $product['stock'] <= 0)
        {
            $this->session->set_flashdata('error', 'This product is currently out of stock.');
            redirect('products');
        }

        $cart = $this->get_cart();
        $existing_quantity = isset($cart[(int) $product['id']]['quantity']) ? (int) $cart[(int) $product['id']]['quantity'] : 0;

        if (($existing_quantity + 1) > (int) $product['stock'])
        {
            $this->session->set_flashdata('error', 'You cannot add more than the available stock.');
            redirect('products');
        }

        $cart[(int) $product['id']] = array(
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $existing_quantity + 1,
        );

        $this->set_cart($cart);
        redirect('cart/checkout');
    }

    public function success()
    {
        $this->require_role(self::ROLE_USER);

        $session_id = $this->input->get('session_id', TRUE);

        if (empty($session_id))
        {
            $this->session->set_flashdata('error', 'Missing payment session information.');
            redirect('products');
        }

        $checkout_session = $this->retrieve_checkout_session($session_id);

        if ($checkout_session === NULL)
        {
            $this->session->set_flashdata('error', 'Unable to verify the payment session.');
            redirect('products');
        }

        $order = $this->get_order_from_session($checkout_session);

        if (empty($order) || (int) $order['user_id'] !== $this->current_user_id())
        {
            show_error('Unauthorized order access.', 403);
        }

        $payment_status = (string) $this->stripe_object_value($checkout_session, 'payment_status', 'unpaid');

        if ($payment_status === 'paid')
        {
            $this->sync_paid_checkout_session($checkout_session);
            $order = $this->Order_model->get_by_id($order['id']);
            $this->clear_cart();
        }

        $payment = $this->Payment_model->get_by_order_id($order['id']);

        $this->load->view('payment/success', array(
            'page_title' => 'Payment Success',
            'order' => $order,
            'payment' => $payment,
            'order_items' => $this->Order_item_model->get_by_order_id($order['id']),
            'invoice' => $this->Invoice_model->get_by_order_id($order['id']),
            'receipt' => $payment ? $this->Receipt_model->get_by_payment_id($payment['id']) : NULL,
            'payment_status' => $payment_status,
        ));
    }

    public function cancel()
    {
        $this->require_role(self::ROLE_USER);

        $order_id = (int) $this->input->get('order_id', TRUE);
        $order = NULL;

        if ($order_id > 0)
        {
            $order = $this->Order_model->get_by_id($order_id);

            if ($order && (int) $order['user_id'] === $this->current_user_id() && $order['status'] === Order_model::STATUS_PENDING)
            {
                $this->Order_model->mark_failed_if_pending($order_id);
                $order = $this->Order_model->get_by_id($order_id);
            }
        }

        $this->load->view('payment/cancel', array(
            'page_title' => 'Payment Cancelled',
            'order' => $order,
        ));
    }

    public function webhook()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            show_error('Invalid request method.', 405);
        }

        if ( ! $this->stripe_is_configured(TRUE))
        {
            show_error('Stripe webhook is not configured.', 500);
        }

        $payload = @file_get_contents('php://input');
        $signature = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

        try
        {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $this->stripe_webhook_secret);
        }
        catch (\UnexpectedValueException $exception)
        {
            log_message('error', 'Stripe webhook payload error: '.$exception->getMessage());
            show_error('Invalid payload.', 400);
        }
        catch (\Stripe\Exception\SignatureVerificationException $exception)
        {
            log_message('error', 'Stripe webhook signature verification failed: '.$exception->getMessage());
            show_error('Invalid signature.', 400);
        }
        catch (Exception $exception)
        {
            log_message('error', 'Stripe webhook general error: '.$exception->getMessage());
            show_error('Webhook error.', 500);
        }

        $type = (string) $this->stripe_object_value($event, 'type', '');
        $session = $this->stripe_object_value($this->stripe_object_value($event, 'data', array()), 'object');

        log_message('info', 'Stripe webhook received: '.$type);

        if ($session)
        {
            if ($type === 'checkout.session.completed' || $type === 'checkout.session.async_payment_succeeded')
            {
                $this->sync_paid_checkout_session($session);
            }
            elseif ($type === 'checkout.session.expired' || $type === 'checkout.session.async_payment_failed')
            {
                $this->sync_failed_checkout_session($session, $type);
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('received' => TRUE)));
    }

    protected function stripe_is_configured($require_webhook_secret = FALSE)
    {
        if (empty($this->stripe_secret) || strpos($this->stripe_secret, 'sk_test_xxx') === 0)
        {
            return FALSE;
        }

        if ($require_webhook_secret && (empty($this->stripe_webhook_secret) || strpos($this->stripe_webhook_secret, 'whsec_xxx') === 0))
        {
            return FALSE;
        }

        return TRUE;
    }

    protected function retrieve_checkout_session($session_id)
    {
        if ( ! $this->stripe_is_configured())
        {
            return NULL;
        }

        try
        {
            \Stripe\Stripe::setApiKey($this->stripe_secret);

            return \Stripe\Checkout\Session::retrieve($session_id);
        }
        catch (Exception $exception)
        {
            log_message('error', 'Stripe session retrieval failed: '.$exception->getMessage());
            return NULL;
        }
    }

    protected function get_order_from_session($checkout_session)
    {
        $metadata = $this->stripe_object_value($checkout_session, 'metadata', array());
        $order_id = (int) $this->stripe_object_value($metadata, 'order_id', 0);

        if ($order_id <= 0)
        {
            return NULL;
        }

        return $this->Order_model->get_by_id($order_id);
    }

    protected function sync_paid_checkout_session($checkout_session)
    {
        $order = $this->get_order_from_session($checkout_session);

        if (empty($order))
        {
            log_message('error', 'Stripe paid session missing order metadata.');
            return FALSE;
        }

        $session_id = (string) $this->stripe_object_value($checkout_session, 'id', '');
        $payment_status = (string) $this->stripe_object_value($checkout_session, 'payment_status', 'paid');
        $amount_total = ((int) $this->stripe_object_value($checkout_session, 'amount_total', 0)) / 100;
        $lock_name = 'portal_order_'.$order['id'];

        if ( ! $this->acquire_lock($lock_name))
        {
            log_message('error', 'Unable to acquire order lock for order '.$order['id']);
            return FALSE;
        }

        try
        {
            $order = $this->Order_model->get_by_id($order['id']);
            $order_items = $this->get_order_items($order);

            $this->db->trans_begin();

            $payment_recorded = $this->Payment_model->record(array(
                'order_id' => $order['id'],
                'stripe_session_id' => $session_id,
                'payment_status' => $payment_status,
                'amount' => $amount_total,
            ));

            if ($payment_recorded === FALSE)
            {
                $this->db->trans_rollback();
                log_message('error', 'Failed to record payment for order '.$order['id']);
                return FALSE;
            }

            if ($order['status'] !== Order_model::STATUS_PAID)
            {
                foreach ($order_items as $item)
                {
                    if ( ! $this->Product_model->reduce_stock($item['product_id'], $item['quantity']))
                    {
                        $this->db->trans_rollback();

                        $this->db->trans_begin();
                        $this->Order_model->mark_failed_if_pending($order['id']);
                        $this->Payment_model->record(array(
                            'order_id' => $order['id'],
                            'stripe_session_id' => $session_id,
                            'payment_status' => 'stock_unavailable',
                            'amount' => $amount_total,
                        ));
                        $this->db->trans_commit();

                        log_message('error', 'Stock update failed for order '.$order['id'].' product '.$item['product_id']);
                        return FALSE;
                    }
                }

                $this->Order_model->mark_paid($order['id']);
            }

            $payment = $this->Payment_model->get_by_session_id($session_id);

            if ($payment)
            {
                $this->Invoice_model->create_for_order($order);
                $this->Receipt_model->create_for_payment($payment);
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                log_message('error', 'Failed to persist paid payment artifacts for order '.$order['id']);
                return FALSE;
            }

            $this->db->trans_commit();

            return TRUE;
        }
        finally
        {
            $this->release_lock($lock_name);
        }
    }

    protected function sync_failed_checkout_session($checkout_session, $status)
    {
        $order = $this->get_order_from_session($checkout_session);

        if (empty($order))
        {
            log_message('error', 'Stripe failed session missing order metadata.');
            return FALSE;
        }

        $session_id = (string) $this->stripe_object_value($checkout_session, 'id', '');
        $amount_total = ((int) $this->stripe_object_value($checkout_session, 'amount_total', 0)) / 100;
        $lock_name = 'portal_order_'.$order['id'];

        if ( ! $this->acquire_lock($lock_name))
        {
            log_message('error', 'Unable to acquire order lock for failed payment order '.$order['id']);
            return FALSE;
        }

        try
        {
            $order = $this->Order_model->get_by_id($order['id']);
            $this->db->trans_begin();

            if ($order['status'] !== Order_model::STATUS_PAID)
            {
                $this->Order_model->mark_failed_if_pending($order['id']);
                $this->Payment_model->record(array(
                    'order_id' => $order['id'],
                    'stripe_session_id' => $session_id,
                    'payment_status' => $status,
                    'amount' => $amount_total,
                ));
            }

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                log_message('error', 'Failed to persist failed payment state for order '.$order['id']);
                return FALSE;
            }

            $this->db->trans_commit();

            return TRUE;
        }
        finally
        {
            $this->release_lock($lock_name);
        }
    }

    protected function get_order_items(array $order)
    {
        $items = $this->Order_item_model->get_by_order_id($order['id']);

        if ( ! empty($items))
        {
            return $items;
        }

        if ( ! empty($order['product_id']))
        {
            return array(array(
                'product_id' => (int) $order['product_id'],
                'quantity' => 1,
                'price' => $order['total_amount'],
                'product_name' => isset($order['product_name']) ? $order['product_name'] : '',
            ));
        }

        return array();
    }

    protected function acquire_lock($name, $timeout = 5)
    {
        $query = $this->db->query('SELECT GET_LOCK(?, ?) AS lock_status', array($name, (int) $timeout));
        $row = $query ? $query->row_array() : array();

        return isset($row['lock_status']) && (string) $row['lock_status'] === '1';
    }

    protected function release_lock($name)
    {
        $this->db->query('SELECT RELEASE_LOCK(?)', array($name));
    }

    protected function stripe_object_value($object, $key, $default = NULL)
    {
        if (is_array($object) && array_key_exists($key, $object))
        {
            return $object[$key];
        }

        if (is_object($object) && isset($object->{$key}))
        {
            return $object->{$key};
        }

        if (is_object($object) && $object instanceof ArrayAccess && isset($object[$key]))
        {
            return $object[$key];
        }

        return $default;
    }

    protected function to_stripe_amount($amount)
    {
        return (int) round(((float) $amount) * 100);
    }
}
