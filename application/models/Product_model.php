<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    protected $table = 'products';
    protected $list_cache_key = 'products_all';
    protected $cache_ttl = 300;

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'file', 'backup' => 'dummy'));
    }

    public function get_all()
    {
        $cached = $this->cache->get($this->list_cache_key);

        if ($cached !== FALSE)
        {
            return $cached;
        }

        $products = $this->db
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get($this->table)
            ->result_array();

        $this->cache->save($this->list_cache_key, $products, $this->cache_ttl);

        return $products;
    }

    public function get_latest($limit = 5)
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit((int) $limit)
            ->get($this->table)
            ->result_array();
    }

    public function get_by_id($id)
    {
        $cache_key = $this->get_item_cache_key($id);
        $cached = $this->cache->get($cache_key);

        if ($cached !== FALSE)
        {
            return $cached;
        }

        $product = $this->db
            ->get_where($this->table, array('id' => (int) $id))
            ->row_array();

        if ($product)
        {
            $this->cache->save($cache_key, $product, $this->cache_ttl);
        }

        return $product;
    }

    public function create(array $payload)
    {
        $data = array(
            'name' => trim($payload['name']),
            'description' => trim($payload['description']),
            'price' => number_format((float) $payload['price'], 2, '.', ''),
            'stock' => max(0, (int) $payload['stock']),
            'created_at' => date('Y-m-d H:i:s'),
        );

        $this->db->insert($this->table, $data);

        if ($this->db->affected_rows() !== 1)
        {
            return FALSE;
        }

        $product_id = (int) $this->db->insert_id();
        $this->clear_cache($product_id);

        return $product_id;
    }

    public function update($id, array $payload)
    {
        $data = array(
            'name' => trim($payload['name']),
            'description' => trim($payload['description']),
            'price' => number_format((float) $payload['price'], 2, '.', ''),
            'stock' => max(0, (int) $payload['stock']),
        );

        $updated = $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);

        if ($updated)
        {
            $this->clear_cache($id);
        }

        return $updated;
    }

    public function delete($id)
    {
        $deleted = $this->db
            ->where('id', (int) $id)
            ->delete($this->table);

        if ($deleted)
        {
            $this->clear_cache($id);
        }

        return $deleted;
    }

    public function count_all()
    {
        return (int) $this->db->count_all($this->table);
    }

    public function get_by_ids(array $product_ids)
    {
        $product_ids = array_values(array_unique(array_map('intval', $product_ids)));

        if (empty($product_ids))
        {
            return array();
        }

        $products = $this->db
            ->where_in('id', $product_ids)
            ->get($this->table)
            ->result_array();

        $indexed = array();

        foreach ($products as $product)
        {
            $indexed[(int) $product['id']] = $product;
        }

        return $indexed;
    }

    public function reduce_stock($product_id, $quantity)
    {
        $quantity = max(0, (int) $quantity);

        if ($quantity <= 0)
        {
            return TRUE;
        }

        $sql = "UPDATE `{$this->table}`
                SET `stock` = `stock` - ?
                WHERE `id` = ? AND `stock` >= ?";

        $this->db->query($sql, array($quantity, (int) $product_id, $quantity));

        if ($this->db->affected_rows() === 1)
        {
            $this->clear_cache((int) $product_id);
            return TRUE;
        }

        return FALSE;
    }

    public function has_available_stock($product_id, $quantity)
    {
        $product = $this->get_by_id($product_id);

        return $product && (int) $product['stock'] >= (int) $quantity;
    }

    protected function clear_cache($id = NULL)
    {
        $this->cache->delete($this->list_cache_key);

        if ($id !== NULL)
        {
            $this->cache->delete($this->get_item_cache_key($id));
        }
    }

    protected function get_item_cache_key($id)
    {
        return 'products_item_'.(int) $id;
    }
}
