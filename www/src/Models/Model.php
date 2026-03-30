<?php
namespace App\Models;

/**
 * Base class for all models.
 * Provides a shared database connection to all child models.
 */
abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new MySQLDatabase();
    }
}
