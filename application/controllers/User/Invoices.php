<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends User_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Invoice_model');
        $this->load->model('Receipt_model');
    }

    public function index()
    {
        $this->load->view('user/invoices/list', array(
            'page_title' => 'My Invoices',
            'invoices' => $this->Invoice_model->get_by_user($this->current_user_id()),
        ));
    }

    public function view($invoice_id)
    {
        $invoice = $this->Invoice_model->get_by_id($invoice_id);

        if (empty($invoice) || (int) $invoice['user_id'] !== $this->current_user_id())
        {
            show_error('Invoice not found.', 404);
        }

        $this->load->view('user/invoices/view', array(
            'page_title' => 'Invoice Details',
            'invoice' => $invoice,
        ));
    }

    public function receipts()
    {
        $this->load->view('user/receipts/list', array(
            'page_title' => 'My Receipts',
            'receipts' => $this->Receipt_model->get_by_user($this->current_user_id()),
        ));
    }

    public function view_receipt($receipt_id)
    {
        $receipt = $this->Receipt_model->get_by_id($receipt_id);

        if (empty($receipt) || (int) $receipt['user_id'] !== $this->current_user_id())
        {
            show_error('Receipt not found.', 404);
        }

        $this->load->view('user/receipts/view', array(
            'page_title' => 'Receipt Details',
            'receipt' => $receipt,
        ));
    }
}
