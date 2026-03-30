<?php


function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /connexion');
        exit;
    }
}

function requireRole(string|array $roles): void
{
    requireLogin();
    $roles = (array) $roles;
    if (!in_array($_SESSION['user_role'] ?? '', $roles)) {
        header('Location: /');
        exit;
    }
}
