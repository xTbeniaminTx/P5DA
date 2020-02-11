<?php

namespace app\models;

use app\services\Database;

class Login
{
    private $db;


    public function __construct()
    {
        $this->db = new Database();
    }


    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM login WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();
        $password_db = $row->password;
        if (password_verify($password, $password_db)) {
            return $row;
        } else {
            return false;
        }
    }

    //------------------------------------------------------------------------------------------------------------------
    public function findByEmail($email)
    {
        $this->db->query('SELECT * FROM login WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        //check row
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    //------------------------------------------------------------------------------------------------------------------

}