<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Middleware\AuthMiddleware;
use App\Model\BookManager;
use App\Model\UserManager;

final class Router
{
    private const PROTECTED_ACTIONS = ['account', 'messages'];

    public function dispatch(string $action, array $params = []): void
    {
        if (in_array($action, self::PROTECTED_ACTIONS, true)) {
            (new AuthMiddleware(new AuthService()))->handle();
        }

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

            case 'login':
                $this->createUserController()->login();
                break;

            case 'logout':
                $this->createUserController()->logout();
                break;

            default:
                throw new NotFoundException();
        }
    }

    private function createBookController(): BookController
    {
        $pdo = DBManager::getInstance()->getPDO();

        return new BookController(
            new BookManager($pdo),
            new View(new CsrfTokenManager(), new AuthService()),
        );
    }

    private function createUserController(): UserController
    {
        $pdo = DBManager::getInstance()->getPDO();
        $csrfTokenManager = new CsrfTokenManager();
        $authService = new AuthService();

        return new UserController(
            new UserManager($pdo),
            new View($csrfTokenManager, $authService),
            $csrfTokenManager,
            $authService,
        );
    }
}
