<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_panel extends Admin_Controller
{
    public function index()
    {
        redirect('admin/dashboard');
    }
}
