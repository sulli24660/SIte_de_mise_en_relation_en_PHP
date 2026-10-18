<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Exception\NotFoundException;

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

            default:
                throw new NotFoundException();
        }
    }
}
