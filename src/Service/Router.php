<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Model\UserManager;

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
                $this->createUserController()->register();
                break;

            default:
                throw new NotFoundException();
        }
    }

    private function createBookController(): BookController
    {
        $pdo = DBManager::getInstance()->getPDO();

        return new BookController(new BookManager($pdo), new View());
    }

    private function createUserController(): UserController
    {
        return new UserController(new UserManager(), new View());
    }
}