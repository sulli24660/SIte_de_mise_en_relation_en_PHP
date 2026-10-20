<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Exception\NotFoundException;
use App\Controller\UserController;

class Router
{
    public function dispatch(string $action, array $params = []): void
    {
        switch ($action) {
            case 'books':
                (new BookController())->index();
                break;

            case 'book':
                (new BookController())->show((int) ($params['id'] ?? 0));
                break;

            case 'register':
                (new UserController())->register();
                break;

            default:
                throw new NotFoundException();
        }
    }
}
