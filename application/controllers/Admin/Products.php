<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Product_model');
        $this->form_validation->set_error_delimiters('<div class="invalid-feedback d-block">', '</div>');
    }

    public function index()
    {
        $this->load->view('admin/products/list', array(
            'page_title' => 'Manage Products',
            'active_nav' => 'products',
            'products' => $this->Product_model->get_all(),
        ));
    }

    public function create()
    {
        if ($this->input->method(TRUE) === 'POST')
        {
            $this->set_product_rules();

            if ($this->form_validation->run())
            {
                $product_id = $this->Product_model->create(array(
                    'name' => $this->input->post('name', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'price' => $this->input->post('price', TRUE),
                    'stock' => $this->input->post('stock', TRUE),
                ));

                if ($product_id !== FALSE)
                {
                    $this->session->set_flashdata('success', 'Product created successfully.');
                    redirect('admin/products');
                }

                $this->session->set_flashdata('error', 'Unable to create the product right now.');
                redirect('admin/products/create');
            }
        }

        $this->load->view('admin/products/create', array(
            'page_title' => 'Create Product',
            'active_nav' => 'products',
        ));
    }

    public function edit($id)
    {
        $product = $this->Product_model->get_by_id($id);

        if (empty($product))
        {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('admin/products');
        }

        if ($this->input->method(TRUE) === 'POST')
        {
            $this->set_product_rules();

            if ($this->form_validation->run())
            {
                $updated = $this->Product_model->update($id, array(
                    'name' => $this->input->post('name', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'price' => $this->input->post('price', TRUE),
                    'stock' => $this->input->post('stock', TRUE),
                ));

                if ($updated)
                {
                    $this->session->set_flashdata('success', 'Product updated successfully.');
                    redirect('admin/products');
                }

                $this->session->set_flashdata('error', 'No changes were saved.');
                redirect('admin/products/edit/'.$id);
            }
        }

        $this->load->view('admin/products/edit', array(
            'page_title' => 'Edit Product',
            'active_nav' => 'products',
            'product' => $product,
        ));
    }

    public function delete($id)
    {
        if ($this->input->method(TRUE) !== 'POST')
        {
            show_error('Invalid request method.', 405);
        }

        $product = $this->Product_model->get_by_id($id);

        if (empty($product))
        {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('admin/products');
        }

        $this->Product_model->delete($id);
        $this->session->set_flashdata('success', 'Product deleted successfully.');
        redirect('admin/products');
    }

    protected function set_product_rules()
    {
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[150]');
        $this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[10]|max_length[1000]');
        $this->form_validation->set_rules('price', 'Price', 'trim|required|decimal|greater_than[0]');
        $this->form_validation->set_rules('stock', 'Stock', 'trim|required|integer|greater_than_equal_to[0]');
    }
}
