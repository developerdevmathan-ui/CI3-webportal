<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_item_model extends CI_Model
{
    protected $table = 'order_items';

    public function create_batch($order_id, array $items)
    {
        $batch = array();

        foreach ($items as $item)
        {
            $batch[] = array(
                'order_id' => (int) $order_id,
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'price' => number_format((float) $item['price'], 2, '.', ''),
            );
        }

        if (empty($batch))
        {
            return FALSE;
        }

        return $this->db->insert_batch($this->table, $batch);
    }

    public function get_by_order_id($order_id)
    {
        return $this->db
            ->select('order_items.*, products.name AS product_name, products.stock AS current_stock')
            ->from($this->table)
            ->join('products', 'products.id = order_items.product_id', 'left')
            ->where('order_items.order_id', (int) $order_id)
            ->order_by('order_items.id', 'ASC')
            ->get()
            ->result_array();
    }
}
