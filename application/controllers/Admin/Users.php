<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->form_validation->set_error_delimiters('<div class="invalid-feedback d-block">', '</div>');
    }

    public function create()
    {
        if ($this->input->method(TRUE) === 'POST')
        {
            $this->set_user_rules();

            if ($this->form_validation->run())
            {
                $user_id = $this->User_model->create_user(array(
                    'name' => $this->input->post('name', TRUE),
                    'email' => $this->input->post('email', TRUE),
                    'password' => (string) $this->input->post('password', FALSE),
                    'role' => $this->input->post('role', TRUE),
                ));

                if ($user_id !== FALSE)
                {
                    $this->session->set_flashdata('success', 'User account created successfully.');
                    redirect('admin/users/create');
                }

                $this->session->set_flashdata('error', 'Unable to create the user right now.');
                redirect('admin/users/create');
            }
        }

        $this->load->view('admin/users/create', array(
            'page_title' => 'Create User',
            'active_nav' => 'users',
            'roles' => array(
                User_model::ROLE_USER => 'User',
                User_model::ROLE_ADMIN => 'Admin',
            ),
        ));
    }

    protected function set_user_rules()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[255]|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[72]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');
        $this->form_validation->set_rules('role', 'Role', 'trim|required|in_list[admin,user]');
    }
}
