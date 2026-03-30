<?php
namespace App\Models;

/**
 * Interface that every database implementation must follow.
 */
interface Database
{
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): bool;
    public function lastInsertId(): int;
}
