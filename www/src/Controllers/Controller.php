<?php
namespace App\Controllers;

use Twig\Environment;

/**
 * Classe de base pour tous les contrôleurs.
 * Fournit l'accès à l'environnement Twig et une propriété modèle partagée.
 */
abstract class Controller
{
    protected Environment $twig;
    protected $model = null;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Rend un template Twig avec les données fournies.
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }

    /**
     * Redirige vers une URL donnée.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
