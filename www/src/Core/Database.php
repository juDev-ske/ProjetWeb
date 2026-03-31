<?php
namespace App\Core;

interface Database
{
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): bool;
    public function lastInsertId(): int;
}
