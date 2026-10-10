<?php

namespace App\Core;

use App\Exceptions\NotFoundException;
use Throwable;

class SuperRouter implements RouterInterface
{
    /** @var array<string, array{class-string, string}> */
    private array $routes = [];

    public function add(string $action, string $controller, string $method): void
    {
        $this->routes[$action] = [$controller, $method];
    }

    public function dispatch(string $action): Response
    {
        try {
            if (!isset($this->routes[$action])) {
                throw new NotFoundException('Page introuvable');
            }

            [$controller, $method] = $this->routes[$action];

            return (new $controller())->$method();
        } catch (NotFoundException $e) {
            return new Response(htmlspecialchars($e->getMessage()), 404);
        } catch (Throwable $e) {
            error_log((string) $e);
            $content = defined('DEBUG') && DEBUG
                ? '<pre>' . htmlspecialchars((string) $e) . '</pre>'
                : 'Une erreur est survenue.';

            return new Response($content, 500);
        }
    }
}
