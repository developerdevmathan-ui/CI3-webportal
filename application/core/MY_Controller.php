<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const CART_SESSION_KEY = 'portal_cart';

    public function __construct()
    {
        parent::__construct();
    }

    protected function is_logged_in()
    {
        return (bool) $this->session->userdata('logged_in');
    }

    protected function current_user_id()
    {
        return (int) $this->session->userdata('user_id');
    }

    protected function current_user_role()
    {
        return (string) $this->session->userdata('role');
    }

    protected function current_user_name()
    {
        return (string) $this->session->userdata('name');
    }

    protected function redirect_authenticated_user()
    {
        if ($this->is_logged_in())
        {
            $this->redirect_to_dashboard();
        }
    }

    protected function require_auth()
    {
        if ( ! $this->is_logged_in())
        {
            $this->session->set_flashdata('error', 'Please log in to continue.');
            redirect('login');
        }
    }

    protected function require_role($role)
    {
        $this->require_auth();

        if ($this->current_user_role() !== $role)
        {
            $this->session->set_flashdata('error', 'You are not authorized to access that page.');
            $this->redirect_to_dashboard();
        }
    }

    protected function redirect_to_dashboard()
    {
        if ($this->current_user_role() === self::ROLE_ADMIN)
        {
            redirect('admin/dashboard');
        }

        if ($this->current_user_role() === self::ROLE_USER)
        {
            redirect('user/dashboard');
        }

        redirect('login');
    }

    protected function get_cart()
    {
        $cart = $this->session->userdata(self::CART_SESSION_KEY);

        return is_array($cart) ? $cart : array();
    }

    protected function set_cart(array $cart)
    {
        $normalized = array();

        foreach ($cart as $item)
        {
            if (empty($item['product_id']) || empty($item['quantity']))
            {
                continue;
            }

            $normalized[(int) $item['product_id']] = array(
                'product_id' => (int) $item['product_id'],
                'name' => isset($item['name']) ? (string) $item['name'] : '',
                'price' => isset($item['price']) ? number_format((float) $item['price'], 2, '.', '') : '0.00',
                'quantity' => max(1, (int) $item['quantity']),
            );
        }

        $this->session->set_userdata(self::CART_SESSION_KEY, $normalized);

        return $normalized;
    }

    protected function clear_cart()
    {
        $this->session->unset_userdata(self::CART_SESSION_KEY);
    }

    protected function cart_item_count()
    {
        $count = 0;

        foreach ($this->get_cart() as $item)
        {
            $count += (int) $item['quantity'];
        }

        return $count;
    }
}

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ( ! $this->is_logged_in() || $this->current_user_role() !== MY_Controller::ROLE_ADMIN)
        {
            $this->session->set_flashdata('error', 'Administrator access is required.');
            redirect('login');
        }
    }
}

class User_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->require_role(MY_Controller::ROLE_USER);
    }
}

class Api_Controller extends CI_Controller
{
    protected $api_user = NULL;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->output->set_content_type('application/json');
    }

    protected function respond($status, $message, $data = array(), $http_code = 200)
    {
        $this->output
            ->set_status_header((int) $http_code)
            ->set_output(json_encode(array(
                'status' => (bool) $status,
                'message' => $message,
                'data' => $data,
            )));
    }

    protected function respond_validation_error($message, $data = array())
    {
        $this->respond(FALSE, $message, $data, 422);
    }

    protected function respond_unauthorized($message = 'Unauthorized access.')
    {
        $this->respond(FALSE, $message, array(), 401);
    }

    protected function require_api_auth()
    {
        $token = $this->get_bearer_token();

        if (empty($token))
        {
            $this->respond_unauthorized('Authorization token is required.');
            return FALSE;
        }

        $user = $this->User_model->get_by_api_token($token);

        if (empty($user))
        {
            $this->respond_unauthorized('Invalid or expired API token.');
            return FALSE;
        }

        $this->api_user = $user;

        return $user;
    }

    protected function current_api_user()
    {
        return $this->api_user;
    }

    protected function current_api_user_id()
    {
        return empty($this->api_user) ? 0 : (int) $this->api_user['id'];
    }

    protected function get_request_payload()
    {
        $raw_input = trim((string) file_get_contents('php://input'));

        if ($raw_input !== '')
        {
            $decoded = json_decode($raw_input, TRUE);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
            {
                return $decoded;
            }
        }

        return $this->input->post(NULL, TRUE) ?: array();
    }

    protected function get_bearer_token()
    {
        $header = $this->get_authorization_header();

        if (empty($header))
        {
            return NULL;
        }

        if (preg_match('/Bearer\s+(.+)/i', $header, $matches))
        {
            return trim($matches[1]);
        }

        return NULL;
    }

    protected function get_authorization_header()
    {
        if ($this->input->server('HTTP_AUTHORIZATION'))
        {
            return trim($this->input->server('HTTP_AUTHORIZATION'));
        }

        if ($this->input->server('REDIRECT_HTTP_AUTHORIZATION'))
        {
            return trim($this->input->server('REDIRECT_HTTP_AUTHORIZATION'));
        }

        if (function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();

            if (isset($headers['Authorization']))
            {
                return trim($headers['Authorization']);
            }

            if (isset($headers['authorization']))
            {
                return trim($headers['authorization']);
            }
        }

        return NULL;
    }
}
