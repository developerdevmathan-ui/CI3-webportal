<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';

    protected $table = 'orders';

    public function create(array $payload)
    {
        $data = array(
            'user_id' => (int) $payload['user_id'],
            'product_id' => (int) $payload['product_id'],
            'total_amount' => number_format((float) $payload['total_amount'], 2, '.', ''),
            'status' => isset($payload['status']) ? $payload['status'] : self::STATUS_PENDING,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() !== 1)
        {
            return FALSE;
        }

        return (int) $this->db->insert_id();
    }

    public function get_by_id($id)
    {
        return $this->base_query()
            ->where('orders.id', (int) $id)
            ->get()
            ->row_array();
    }

    public function get_by_user($user_id, $limit = NULL)
    {
        $this->base_query()
            ->where('orders.user_id', (int) $user_id)
            ->order_by('orders.created_at', 'DESC')
            ->order_by('orders.id', 'DESC');

        if ($limit !== NULL)
        {
            $this->db->limit((int) $limit);
        }

        return $this->db
            ->get()
            ->result_array();
    }

    public function get_recent_pending_by_user_product($user_id, $product_id, $minutes = 10)
    {
        return $this->db
            ->from($this->table)
            ->where('user_id', (int) $user_id)
            ->where('product_id', (int) $product_id)
            ->where('status', self::STATUS_PENDING)
            ->where('created_at >=', date('Y-m-d H:i:s', time() - (((int) $minutes) * 60)))
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function update_status($id, $status)
    {
        $updated = $this->db
            ->where('id', (int) $id)
            ->update($this->table, array('status' => $status));

        return $updated && $this->db->affected_rows() >= 0;
    }

    public function mark_paid($id)
    {
        $updated = $this->db
            ->where('id', (int) $id)
            ->where('status !=', self::STATUS_PAID)
            ->update($this->table, array('status' => self::STATUS_PAID));

        return $updated && $this->db->affected_rows() === 1;
    }

    public function mark_failed_if_pending($id)
    {
        $updated = $this->db
            ->where('id', (int) $id)
            ->where('status', self::STATUS_PENDING)
            ->update($this->table, array('status' => self::STATUS_FAILED));

        return $updated && $this->db->affected_rows() === 1;
    }

    protected function base_query()
    {
        $items_summary_sql = '(SELECT oi.order_id, GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) ORDER BY oi.id SEPARATOR ", ") AS item_summary
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            GROUP BY oi.order_id) order_item_summary';

        return $this->db
            ->select('orders.*, COALESCE(order_item_summary.item_summary, products.name) AS product_name, products.description AS product_description')
            ->from($this->table)
            ->join('products', 'products.id = orders.product_id', 'left')
            ->join($items_summary_sql, 'order_item_summary.order_id = orders.id', 'left', FALSE);
    }
}
