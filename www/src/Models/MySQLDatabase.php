<?php
namespace App\Models;

use PDO;
use PDOException;

/**
 * MySQL implementation of the Database interface.
 * Uses PDO to connect and interact with the MySQL database.
 */
class MySQLDatabase implements Database
{
    private PDO $pdo;

    public function __construct()
    {
        $host     = 'db';           // nom du conteneur Docker MySQL
        $dbname   = 'monsite';
        $user     = 'user';
        $password = 'userpass';
        $charset  = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    /**
     * Execute a SELECT query and return all results.
     * Example: $db->query("SELECT * FROM offer WHERE is_active = ?", [1]);
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute an INSERT, UPDATE or DELETE query.
     * Example: $db->execute("INSERT INTO user (email, password) VALUES (?, ?)", [$email, $hash]);
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Returns the ID of the last inserted row.
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
