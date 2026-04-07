<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends User_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
    }

    public function index()
    {
        $this->load->view('products/list', array(
            'page_title' => 'Browse Products',
            'products' => $this->Product_model->get_all(),
        ));
    }
}
