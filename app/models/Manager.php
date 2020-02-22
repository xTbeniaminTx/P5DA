<?php


namespace app\models;

use app\services\CSRFToken;
use app\services\Database;

class Manager
{
    protected $db;
    protected $token;


    public function __construct()
    {
        $this->db = new Database;
        $this->token = new CSRFToken();
    }
}