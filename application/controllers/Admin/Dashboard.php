<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Product_model');
    }

    public function index()
    {
        $this->load->view('admin/dashboard', array(
            'page_title' => 'Admin Dashboard',
            'active_nav' => 'dashboard',
            'stats' => array(
                'users' => $this->User_model->count_all(),
                'products' => $this->Product_model->count_all(),
            ),
            'latest_products' => $this->Product_model->get_latest(5),
        ));
    }
}
