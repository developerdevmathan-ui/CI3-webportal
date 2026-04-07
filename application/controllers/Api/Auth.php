<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            return $this->respond(FALSE, 'Method not allowed.', array(), 405);
        }

        $payload = $this->get_request_payload();
        $email = isset($payload['email']) ? trim($payload['email']) : '';
        $password = isset($payload['password']) ? (string) $payload['password'] : '';

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return $this->respond_validation_error('A valid email address is required.');
        }

        if ($password === '')
        {
            return $this->respond_validation_error('Password is required.');
        }

        $user = $this->User_model->authenticate($email, $password);

        if (empty($user))
        {
            return $this->respond_unauthorized('Invalid email or password.');
        }

        $token = $this->User_model->generate_api_token($user['id']);

        if ($token === FALSE)
        {
            return $this->respond(FALSE, 'Unable to generate API token.', array(), 500);
        }

        return $this->respond(TRUE, 'Login successful.', array(
            'token' => $token,
            'user' => array(
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ),
        ));
    }
}
