<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipts extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Receipt_model');
    }

    public function index()
    {
        if ($this->input->method(TRUE) !== 'GET')
        {
            return $this->respond(FALSE, 'Method not allowed.', array(), 405);
        }

        if ($this->require_api_auth() === FALSE)
        {
            return;
        }

        $receipts = $this->Receipt_model->get_by_user($this->current_api_user_id());

        return $this->respond(TRUE, 'Receipts retrieved successfully.', $receipts);
    }
}
