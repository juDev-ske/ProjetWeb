<?php
namespace App\Core;

class Router {
    private string $url;
    private array $routes = [];

    public function __construct(string $url) {
        $this->url = $url;
    }

    public function get(string $path, callable $callable): self {
        $this->routes['GET'][] = new Route($path, $callable);
        return $this;
    }

    public function post(string $path, callable $callable): self {
        $this->routes['POST'][] = new Route($path, $callable);
        return $this;
    }

    public function run(): void {
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo '404 - Page non trouvée';
            return;
        }

        foreach ($this->routes[$method] as $route) {
            if ($route->match($this->url)) {
                $route->call();
                return;
            }
        }

        http_response_code(404);
        echo '404 - Page non trouvée';
    }
}
