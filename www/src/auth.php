<?php

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
 */
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /connexion');
        exit;
    }
}

/**
 * Redirige vers la page d'accueil si l'utilisateur n'a pas le rôle requis.
 * Usage : requireRole('admin') ou requireRole(['admin', 'pilote'])
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
