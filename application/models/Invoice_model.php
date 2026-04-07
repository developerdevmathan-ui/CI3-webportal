<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    protected $table = 'invoices';

    public function get_by_id($id)
    {
        return $this->base_query()
            ->where('invoices.id', (int) $id)
            ->get()
            ->row_array();
    }

    public function get_by_order_id($order_id)
    {
        return $this->base_query()
            ->where('invoices.order_id', (int) $order_id)
            ->get()
            ->row_array();
    }

    public function get_all()
    {
        return $this->base_query()
            ->order_by('invoices.created_at', 'DESC')
            ->order_by('invoices.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_user($user_id)
    {
        return $this->base_query()
            ->where('orders.user_id', (int) $user_id)
            ->order_by('invoices.created_at', 'DESC')
            ->order_by('invoices.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function create_for_order(array $order)
    {
        $existing = $this->get_by_order_id($order['id']);

        if ($existing)
        {
            return $existing;
        }

        $created_at = date('Y-m-d H:i:s');
        $temporary_number = 'INV-TEMP-'.uniqid();

        $inserted = $this->db->query(
            "INSERT IGNORE INTO `{$this->table}` (`order_id`, `invoice_number`, `amount`, `created_at`) VALUES (?, ?, ?, ?)",
            array(
                (int) $order['id'],
                $temporary_number,
                number_format((float) $order['total_amount'], 2, '.', ''),
                $created_at,
            )
        );

        if ($inserted === FALSE)
        {
            return FALSE;
        }

        if ($this->db->affected_rows() !== 1)
        {
            return $this->get_by_order_id($order['id']);
        }

        $invoice_id = (int) $this->db->insert_id();
        $invoice_number = $this->generate_document_number('INV', $created_at, $invoice_id);

        $this->db
            ->where('id', $invoice_id)
            ->update($this->table, array('invoice_number' => $invoice_number));

        return $this->get_by_id($invoice_id);
    }

    protected function base_query()
    {
        $items_summary_sql = '(SELECT oi.order_id, GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) ORDER BY oi.id SEPARATOR ", ") AS item_summary
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            GROUP BY oi.order_id) order_item_summary';

        return $this->db
            ->select('invoices.*, orders.user_id, orders.product_id, orders.status AS order_status, users.name AS user_name, users.email AS user_email, COALESCE(order_item_summary.item_summary, products.name) AS product_name, products.description AS product_description')
            ->from($this->table)
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.user_id')
            ->join('products', 'products.id = orders.product_id', 'left')
            ->join($items_summary_sql, 'order_item_summary.order_id = orders.id', 'left', FALSE);
    }

    protected function generate_document_number($prefix, $created_at, $id)
    {
        return $prefix.'-'.date('Ymd', strtotime($created_at)).'-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }
}
