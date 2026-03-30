<?php
namespace App\Models;

abstract class Model
{
    protected Database $db;
    public function __construct()
    {
        $this->db = new MySQLDatabase();
    }
}
