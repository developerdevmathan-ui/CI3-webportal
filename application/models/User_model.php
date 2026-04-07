<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    protected $table = 'users';
    protected $password_options = array('cost' => 12);

    public function create_user(array $payload)
    {
        $role = isset($payload['role']) ? strtolower(trim($payload['role'])) : self::ROLE_USER;

        if ( ! $this->is_valid_role($role))
        {
            return FALSE;
        }

        $data = array(
            'name' => trim($payload['name']),
            'email' => strtolower(trim($payload['email'])),
            'password' => password_hash($payload['password'], PASSWORD_BCRYPT, $this->password_options),
            'role' => $role,
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
        return $this->db
            ->get_where($this->table, array('id' => (int) $id))
            ->row_array();
    }

    public function get_by_email($email)
    {
        return $this->db
            ->get_where($this->table, array('email' => strtolower(trim($email))))
            ->row_array();
    }

    public function get_by_api_token($token)
    {
        return $this->db
            ->get_where($this->table, array('api_token' => trim($token)))
            ->row_array();
    }

    public function get_all()
    {
        return $this->db
            ->order_by('created_at', 'DESC')
            ->get($this->table)
            ->result_array();
    }

    public function count_all()
    {
        return (int) $this->db->count_all($this->table);
    }

    public function authenticate($email, $password)
    {
        $user = $this->get_by_email($email);

        if (empty($user) || ! password_verify($password, $user['password']))
        {
            return NULL;
        }

        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, $this->password_options))
        {
            $this->db
                ->where('id', (int) $user['id'])
                ->update($this->table, array(
                    'password' => password_hash($password, PASSWORD_BCRYPT, $this->password_options),
                ));
        }

        return $user;
    }

    public function generate_api_token($user_id)
    {
        try
        {
            $token = bin2hex(random_bytes(32));
        }
        catch (Exception $exception)
        {
            log_message('error', 'API token generation failed: '.$exception->getMessage());
            return FALSE;
        }

        $updated = $this->db
            ->where('id', (int) $user_id)
            ->update($this->table, array('api_token' => $token));

        return $updated ? $token : FALSE;
    }

    public function clear_api_token($user_id)
    {
        return $this->db
            ->where('id', (int) $user_id)
            ->update($this->table, array('api_token' => NULL));
    }

    public function is_valid_role($role)
    {
        return in_array($role, array(self::ROLE_ADMIN, self::ROLE_USER), TRUE);
    }
}
