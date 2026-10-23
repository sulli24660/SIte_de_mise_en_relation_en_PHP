<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Exception\NotFoundException;
use App\Controller\UserController;
use App\Request\CreateUserRequest;
use App\Validator\Validator;

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
                (new UserController())->register(
                    new CreateUserRequest(new Validator(DBManager::getInstance()->getPDO()))
                );
                break;

            default:
                throw new NotFoundException();
        }
    }
}
