<?php

namespace App\Core;

interface RouterInterface
{
    public function add(string $action, string $controller, string $method): void;
    public function dispatch(string $action): Response;
}
