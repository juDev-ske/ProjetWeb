<?php
namespace App\Controllers;

use Twig\Environment;

/**
 * Base class for all controllers.
 * Provides access to Twig and the model.
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
     * Render a Twig template with data.
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }

    /**
     * Redirect to a given URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
