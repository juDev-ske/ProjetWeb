<?php
namespace App\Models;

/**
 * Classe de base pour tous les modèles.
 * Fournit une connexion à la base de données partagée pour les modèles enfants.
 */
abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new MySQLDatabase();
    }
}
