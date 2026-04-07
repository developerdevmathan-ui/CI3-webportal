<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    protected $table = 'payments';

    public function get_by_id($id)
    {
        return $this->db
            ->get_where($this->table, array('id' => (int) $id))
            ->row_array();
    }

    public function get_by_session_id($session_id)
    {
        return $this->db
            ->get_where($this->table, array('stripe_session_id' => $session_id))
            ->row_array();
    }

    public function record(array $payload)
    {
        $sql = "INSERT INTO `{$this->table}` (`order_id`, `stripe_session_id`, `payment_status`, `amount`, `created_at`)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    `order_id` = VALUES(`order_id`),
                    `payment_status` = VALUES(`payment_status`),
                    `amount` = VALUES(`amount`)";

        return $this->db->query($sql, array(
            (int) $payload['order_id'],
            $payload['stripe_session_id'],
            $payload['payment_status'],
            number_format((float) $payload['amount'], 2, '.', ''),
            date('Y-m-d H:i:s'),
        ));
    }

    public function get_by_order_id($order_id)
    {
        return $this->db
            ->get_where($this->table, array('order_id' => (int) $order_id))
            ->row_array();
    }
}
