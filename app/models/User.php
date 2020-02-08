<?php


namespace app\models;

use app\libraries\Database;

class User
{

    private $db;

    //------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {
        $this->db = new Database;
    }

    //------------------------------------------------------------------------------------------------------------------

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


}