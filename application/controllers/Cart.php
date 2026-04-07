<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends User_Controller
{
    protected $stripe_secret;
    protected $stripe_currency;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
        $this->load->model('Order_model');
        $this->load->model('Order_item_model');

        $this->stripe_secret = $this->config->item('stripe_secret');
        $this->stripe_currency = $this->config->item('stripe_currency') ?: 'usd';
    }

    public function add($product_id)
    {
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

        $quantity = max(1, (int) $this->input->post('quantity', TRUE));

        if ((int) $product['stock'] <= 0)
        {
            $this->session->set_flashdata('error', 'This product is currently out of stock.');
            redirect('products');
        }

        $cart = $this->get_cart();
        $existing_quantity = isset($cart[(int) $product['id']]['quantity']) ? (int) $cart[(int) $product['id']]['quantity'] : 0;
        $new_quantity = $existing_quantity + $quantity;

        if ($new_quantity > (int) $product['stock'])
        {
            $this->session->set_flashdata('error', 'You cannot add more than the available stock.');
            redirect('products');
        }

        $cart[(int) $product['id']] = array(
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $new_quantity,
        );

        $this->set_cart($cart);
        $this->session->set_flashdata('success', $product['name'].' added to cart.');
        redirect('products');
    }

    public function view()
    {
        $cart_state = $this->build_cart_state();

        $this->load->view('cart/view', array(
            'page_title' => 'My Cart',
            'cart_items' => $cart_state['items'],
            'cart_total' => $cart_state['total'],
        ));
    }

    public function update()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            show_error('Invalid request method.', 405);
        }

        $cart = $this->get_cart();
        $quantities = $this->input->post('quantities');

        if (empty($cart) || ! is_array($quantities))
        {
            $this->session->set_flashdata('error', 'Your cart could not be updated.');
            redirect('cart');
        }

        $products = $this->Product_model->get_by_ids(array_keys($cart));
        $updated_cart = array();
        $messages = array();

        foreach ($cart as $product_id => $item)
        {
            $requested_quantity = isset($quantities[$product_id]) ? (int) $quantities[$product_id] : (int) $item['quantity'];

            if ($requested_quantity <= 0)
            {
                continue;
            }

            if (empty($products[$product_id]) || (int) $products[$product_id]['stock'] <= 0)
            {
                $messages[] = 'One or more items were removed because stock is no longer available.';
                continue;
            }

            $final_quantity = min($requested_quantity, (int) $products[$product_id]['stock']);

            if ($final_quantity !== $requested_quantity)
            {
                $messages[] = 'Some quantities were adjusted to match available stock.';
            }

            $updated_cart[$product_id] = array(
                'product_id' => (int) $products[$product_id]['id'],
                'name' => $products[$product_id]['name'],
                'price' => $products[$product_id]['price'],
                'quantity' => $final_quantity,
            );
        }

        $this->set_cart($updated_cart);

        if (empty($updated_cart))
        {
            $this->session->set_flashdata('error', 'Your cart is now empty.');
        }
        elseif (empty($messages))
        {
            $this->session->set_flashdata('success', 'Cart updated successfully.');
        }
        else
        {
            $this->session->set_flashdata('error', implode(' ', array_unique($messages)));
        }

        redirect('cart');
    }

    public function remove($product_id)
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            show_error('Invalid request method.', 405);
        }

        $cart = $this->get_cart();

        if (isset($cart[(int) $product_id]))
        {
            unset($cart[(int) $product_id]);
            $this->set_cart($cart);
            $this->session->set_flashdata('success', 'Item removed from cart.');
        }

        redirect('cart');
    }

    public function checkout()
    {
        $cart_state = $this->build_cart_state();

        if (empty($cart_state['items']))
        {
            $this->session->set_flashdata('error', 'Your cart is empty.');
            redirect('products');
        }

        if ($this->input->method(TRUE) === 'POST')
        {
            if ( ! $this->stripe_is_configured())
            {
                $this->session->set_flashdata('error', 'Stripe is not configured yet. Please update the project configuration.');
                redirect('cart');
            }

            $stock_error = $this->validate_checkout_stock($cart_state['items']);

            if ($stock_error !== NULL)
            {
                $this->session->set_flashdata('error', $stock_error);
                redirect('cart');
            }

            $this->db->trans_begin();

            $order_id = $this->Order_model->create(array(
                'user_id' => $this->current_user_id(),
                'product_id' => (int) $cart_state['items'][0]['product_id'],
                'total_amount' => $cart_state['total'],
                'status' => Order_model::STATUS_PENDING,
            ));

            if ($order_id === FALSE || ! $this->Order_item_model->create_batch($order_id, $cart_state['items']))
            {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Unable to create your order right now. Please try again.');
                redirect('cart/checkout');
            }

            $this->db->trans_commit();

            try
            {
                \Stripe\Stripe::setApiKey($this->stripe_secret);

                $line_items = array();

                foreach ($cart_state['items'] as $item)
                {
                    $line_items[] = array(
                        'quantity' => (int) $item['quantity'],
                        'price_data' => array(
                            'currency' => $this->stripe_currency,
                            'unit_amount' => $this->to_stripe_amount($item['price']),
                            'product_data' => array(
                                'name' => $item['name'],
                            ),
                        ),
                    );
                }

                $checkout_session = \Stripe\Checkout\Session::create(
                    array(
                        'mode' => 'payment',
                        'success_url' => site_url('payment/success').'?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url' => site_url('payment/cancel').'?order_id='.$order_id,
                        'client_reference_id' => (string) $this->current_user_id(),
                        'metadata' => array(
                            'order_id' => (string) $order_id,
                            'user_id' => (string) $this->current_user_id(),
                            'item_count' => (string) count($cart_state['items']),
                        ),
                        'line_items' => $line_items,
                    ),
                    array('idempotency_key' => 'checkout_order_'.$order_id)
                );

                redirect($checkout_session->url, 'location', 303);
            }
            catch (Exception $exception)
            {
                $this->Order_model->mark_failed_if_pending($order_id);
                log_message('error', 'Stripe cart checkout creation failed: '.$exception->getMessage());
                $this->session->set_flashdata('error', 'Checkout could not be started. Please try again later.');
                redirect('cart/checkout');
            }
        }

        $this->load->view('cart/checkout', array(
            'page_title' => 'Checkout',
            'cart_items' => $cart_state['items'],
            'cart_total' => $cart_state['total'],
        ));
    }

    protected function build_cart_state()
    {
        $cart = $this->get_cart();

        if (empty($cart))
        {
            return array('items' => array(), 'total' => 0);
        }

        $products = $this->Product_model->get_by_ids(array_keys($cart));
        $items = array();
        $total = 0;

        foreach ($cart as $product_id => $cart_item)
        {
            if (empty($products[$product_id]))
            {
                continue;
            }

            $product = $products[$product_id];
            $quantity = min((int) $cart_item['quantity'], max(0, (int) $product['stock']));

            if ($quantity <= 0)
            {
                continue;
            }

            $item_total = ((float) $product['price']) * $quantity;
            $items[] = array(
                'product_id' => (int) $product['id'],
                'name' => $product['name'],
                'price' => number_format((float) $product['price'], 2, '.', ''),
                'stock' => (int) $product['stock'],
                'quantity' => $quantity,
                'item_total' => number_format($item_total, 2, '.', ''),
            );
            $total += $item_total;
        }

        return array(
            'items' => $items,
            'total' => number_format($total, 2, '.', ''),
        );
    }

    protected function validate_checkout_stock(array $items)
    {
        foreach ($items as $item)
        {
            if ((int) $item['quantity'] <= 0)
            {
                return 'Your cart contains an invalid quantity.';
            }

            if ((int) $item['stock'] < (int) $item['quantity'])
            {
                return 'One or more cart items no longer have enough stock available.';
            }
        }

        return NULL;
    }

    protected function stripe_is_configured()
    {
        return ! empty($this->stripe_secret) && strpos($this->stripe_secret, 'sk_test_xxx') !== 0;
    }

    protected function to_stripe_amount($amount)
    {
        return (int) round(((float) $amount) * 100);
    }
}
