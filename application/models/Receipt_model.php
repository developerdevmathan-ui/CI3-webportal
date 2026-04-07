<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipt_model extends CI_Model
{
    protected $table = 'receipts';

    public function get_by_id($id)
    {
        return $this->base_query()
            ->where('receipts.id', (int) $id)
            ->get()
            ->row_array();
    }

    public function get_by_payment_id($payment_id)
    {
        return $this->base_query()
            ->where('receipts.payment_id', (int) $payment_id)
            ->get()
            ->row_array();
    }

    public function get_all()
    {
        return $this->base_query()
            ->order_by('receipts.created_at', 'DESC')
            ->order_by('receipts.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_by_user($user_id)
    {
        return $this->base_query()
            ->where('orders.user_id', (int) $user_id)
            ->order_by('receipts.created_at', 'DESC')
            ->order_by('receipts.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function create_for_payment(array $payment)
    {
        $existing = $this->get_by_payment_id($payment['id']);

        if ($existing)
        {
            return $existing;
        }

        $created_at = date('Y-m-d H:i:s');
        $temporary_number = 'REC-TEMP-'.uniqid();

        $inserted = $this->db->query(
            "INSERT IGNORE INTO `{$this->table}` (`payment_id`, `receipt_number`, `created_at`) VALUES (?, ?, ?)",
            array(
                (int) $payment['id'],
                $temporary_number,
                $created_at,
            )
        );

        if ($inserted === FALSE)
        {
            return FALSE;
        }

        if ($this->db->affected_rows() !== 1)
        {
            return $this->get_by_payment_id($payment['id']);
        }

        $receipt_id = (int) $this->db->insert_id();
        $receipt_number = $this->generate_document_number('REC', $created_at, $receipt_id);

        $this->db
            ->where('id', $receipt_id)
            ->update($this->table, array('receipt_number' => $receipt_number));

        return $this->get_by_id($receipt_id);
    }

    protected function base_query()
    {
        $items_summary_sql = '(SELECT oi.order_id, GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) ORDER BY oi.id SEPARATOR ", ") AS item_summary
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            GROUP BY oi.order_id) order_item_summary';

        return $this->db
            ->select('receipts.*, payments.order_id, payments.payment_status, payments.amount, payments.stripe_session_id, orders.user_id, orders.product_id, users.name AS user_name, users.email AS user_email, COALESCE(order_item_summary.item_summary, products.name) AS product_name')
            ->from($this->table)
            ->join('payments', 'payments.id = receipts.payment_id')
            ->join('orders', 'orders.id = payments.order_id')
            ->join('users', 'users.id = orders.user_id')
            ->join('products', 'products.id = orders.product_id', 'left')
            ->join($items_summary_sql, 'order_item_summary.order_id = orders.id', 'left', FALSE);
    }

    protected function generate_document_number($prefix, $created_at, $id)
    {
        return $prefix.'-'.date('Ymd', strtotime($created_at)).'-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }
}
