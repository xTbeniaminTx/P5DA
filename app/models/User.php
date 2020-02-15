<?php


namespace app\models;

use app\services\Database;

class User
{

    private $db;


    public function __construct()
    {
        $this->db = new Database;
    }


    public function addUser($data)
    {
        $this->db->query('INSERT INTO users (first_name, last_name, password, email, role)
                              VALUES(:first_name, :last_name, :password, :email, :role)');
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':email', $data['email']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function findByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');

        $this->db->bind(':email', $email);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }

    }

    public function findById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');

        $this->db->bind(':id', $id);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }

    }


    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();
        $password_db = $row->password;
        if (password_verify($password, $password_db)) {
            return $row;
        } else {
            return false;
        }
    }

}