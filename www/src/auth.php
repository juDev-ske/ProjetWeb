<?php

/**
 * Redirect to login if user is not connected.
 */
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /connexion');
        exit;
    }
}

/**
 * Redirect to home if user doesn't have the required role.
 * Usage: requireRole('admin') or requireRole(['admin', 'pilote'])
 */
function requireRole(string|array $roles): void
{
    requireLogin();
    $roles = (array) $roles;
    if (!in_array($_SESSION['user_role'] ?? '', $roles)) {
        header('Location: /');
        exit;
    }
}
