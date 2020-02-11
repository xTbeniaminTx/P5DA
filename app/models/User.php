<?php


namespace app\models;

use app\services\Database;

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
        $this->db->query('INSERT INTO users (first_name, last_name, password, email)
                              VALUES(:first_name, :last_name, :password, :email)');
        $this->db->bind(':first_name', $data['first_name']);
        $this->db->bind(':last_name', $data['last_name']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':email', $data['email']);

        //execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }


}