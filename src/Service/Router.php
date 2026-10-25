<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Model\BookManager;

final class Router
{
    public function dispatch(string $action, array $params = []): void
    {
        switch ($action) {
            case 'books':
                $this->createBookController()->index();
                break;

            case 'book':
                $this->createBookController()->show((int) ($params['id'] ?? 0));
                break;

            case 'register':
                (new UserController())->register();
                break;

            default:
                throw new NotFoundException();
        }
    }

    private function createBookController(): BookController
    {
        return new BookController(new BookManager(), new View());
    }
}