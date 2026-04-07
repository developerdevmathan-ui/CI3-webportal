<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->form_validation->set_error_delimiters('<div class="field-error">', '</div>');
    }

    public function index()
    {
        if ($this->is_logged_in())
        {
            $this->redirect_to_dashboard();
        }

        redirect('login');
    }

    public function register()
    {
        $this->redirect_authenticated_user();

        if ($this->input->method(TRUE) === 'POST')
        {
            $this->set_registration_rules();

            if ($this->form_validation->run())
            {
                $user_id = $this->User_model->create_user(array(
                    'name' => $this->input->post('name', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'password' => (string) $this->input->post('password', FALSE),
                    'role' => User_model::ROLE_USER,
                ));

                if ($user_id !== FALSE)
                {
                    $this->session->set_flashdata('success', 'Your account has been created. Please log in.');
                    redirect('login');
                }

                $this->session->set_flashdata('error', 'We could not create your account. Please try again.');
                redirect('register');
            }
        }

        $this->load->view('auth/register', array(
            'page_title' => 'Create User Account',
        ));
    }

    public function login()
    {
        $this->handle_login(User_model::ROLE_USER);
    }

    public function admin_login()
    {
        $this->handle_login(User_model::ROLE_ADMIN);
    }

    public function logout()
    {
        if ($this->is_logged_in())
        {
            $this->session->unset_userdata(array('user_id', 'name', 'role', 'logged_in'));
            $this->session->sess_regenerate(TRUE);
        }

        $this->session->set_flashdata('success', 'You have been logged out successfully.');
        redirect('login');
    }

    protected function handle_login($expected_role)
    {
        $this->redirect_authenticated_user();

        $view_data = $this->get_login_view_data($expected_role);

        if ($this->input->method(TRUE) === 'POST')
        {
            $this->set_login_rules();

            if ($this->form_validation->run())
            {
                $user = $this->User_model->authenticate(
                    $this->input->post('email', TRUE),
                    (string) $this->input->post('password', FALSE)
                );

                if ($user && $user['role'] === $expected_role)
                {
                    $this->session->sess_regenerate(TRUE);
                    $this->session->set_userdata(array(
                        'user_id' => (int) $user['id'],
                        'name' => $user['name'],
                        'role' => $user['role'],
                        'logged_in' => TRUE,
                    ));

                    $this->session->set_flashdata('success', 'Welcome back, '.$user['name'].'.');
                    $this->redirect_to_dashboard();
                }

                $this->session->set_flashdata('error', 'Invalid credentials for this portal.');
                redirect($view_data['login_action']);
            }
        }

        $this->load->view('auth/login', $view_data);
    }

    protected function set_registration_rules()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[255]|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[72]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');
    }

    protected function set_login_rules()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[255]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[72]');
    }

    protected function get_login_view_data($role)
    {
        if ($role === User_model::ROLE_ADMIN)
        {
            return array(
                'page_title' => 'Admin Login',
                'heading' => 'Admin access',
                'description' => 'Sign in',
                'login_action' => 'admin/login',
                'submit_label' => 'Log In as Admin',
                'alternate_label' => 'User login',
                'alternate_url' => site_url('login'),
                'show_register_link' => FALSE,
            );
        }

        return array(
            'page_title' => 'User Login',
            'heading' => 'User access',
            'description' => 'Sign in',
            'login_action' => 'login',
            'submit_label' => 'Log In',
            'alternate_label' => 'Admin login',
            'alternate_url' => site_url('admin/login'),
            'show_register_link' => TRUE,
        );
    }
}
