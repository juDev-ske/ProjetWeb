<?php
namespace App\Core;

use Twig\Environment;

abstract class Controller
{
    protected Environment $twig;
    protected $model = null;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
