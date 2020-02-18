<?php


namespace app\models;

use app\services\Database;

class Manager
{
    protected $db;


    public function __construct()
    {
        $this->db = new Database;
    }
}