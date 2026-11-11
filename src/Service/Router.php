<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Middleware\AuthMiddleware;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Request\Request;

final class Router
{
    private const PROTECTED_ACTIONS = ['account', 'messages'];

    public function dispatch(Request $request): void
    {
        $action = $request->query('action', 'books');

        if (in_array($action, self::PROTECTED_ACTIONS, true)) {
            (new AuthMiddleware(new AuthService()))->handle();
        }

        switch ($action) {
            case 'books':
                $this->createBookController()->index();
                break;

            case 'book':
                $this->createBookController()->show($request->queryInt('id'));
                break;

            case 'register':
                $this->createUserController($request)->register();
                break;

            case 'login':
                $this->createUserController($request)->login();
                break;

            case 'logout':
                $this->createUserController($request)->logout();
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

    private function createUserController(Request $request): UserController
    {
        $pdo = DBManager::getInstance()->getPDO();
        $userManager = new UserManager($pdo);
        $csrfTokenManager = new CsrfTokenManager();
        $authService = new AuthService();

        return new UserController(
            $userManager,
            new UserService($userManager),
            new View($csrfTokenManager, $authService),
            $csrfTokenManager,
            $authService,
            $request,
        );
    }
}