<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Order_model');
    }

    public function index()
    {
        $this->load->view('user/dashboard', array(
            'current_user' => $this->User_model->get_by_id($this->current_user_id()),
            'recent_orders' => $this->Order_model->get_by_user($this->current_user_id(), 10),
        ));
    }
}
