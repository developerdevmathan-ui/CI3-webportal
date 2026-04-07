<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Invoice_model');
        $this->load->model('Receipt_model');
    }

    public function index()
    {
        $this->load->view('admin/invoices/list', array(
            'page_title' => 'All Invoices',
            'active_nav' => 'invoices',
            'invoices' => $this->Invoice_model->get_all(),
        ));
    }

    public function view($invoice_id)
    {
        $invoice = $this->Invoice_model->get_by_id($invoice_id);

        if (empty($invoice))
        {
            show_error('Invoice not found.', 404);
        }

        $this->load->view('admin/invoices/view', array(
            'page_title' => 'Invoice Details',
            'active_nav' => 'invoices',
            'invoice' => $invoice,
        ));
    }

    public function receipts()
    {
        $this->load->view('admin/receipts/list', array(
            'page_title' => 'All Receipts',
            'active_nav' => 'receipts',
            'receipts' => $this->Receipt_model->get_all(),
        ));
    }

    public function view_receipt($receipt_id)
    {
        $receipt = $this->Receipt_model->get_by_id($receipt_id);

        if (empty($receipt))
        {
            show_error('Receipt not found.', 404);
        }

        $this->load->view('admin/receipts/view', array(
            'page_title' => 'Receipt Details',
            'active_nav' => 'receipts',
            'receipt' => $receipt,
        ));
    }
}
