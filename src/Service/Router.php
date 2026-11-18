<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Middleware\AuthMiddleware;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\Request;
use App\Validator\Validator;

final class Router
{
    private const PROTECTED_ACTIONS = ['account', 'messages'];

    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
        private readonly View $view,
    ) {
    }

    public function dispatch(Request $request): void
    {
        $action = $request->query('action', 'books');

        if (in_array($action, self::PROTECTED_ACTIONS, true)) {
            (new AuthMiddleware($this->authService))->handle();
        }

        switch ($action) {
            case 'books':
                $this->createBookController()->index();
                break;

            case 'book':
                $this->createBookController()->show($request->queryInt('id'));
                break;

            case 'register':
                $this->createUserController($request)->register($this->createRegisterRequest());
                break;

            case 'login':
                $this->createUserController($request)->login($this->createLoginRequest());
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
        return new BookController(new BookManager($this->getPdo()), $this->view);
    }

    private function createUserController(Request $request): UserController
    {
        return new UserController(
            new UserService(new UserManager($this->getPdo())),
            $this->view,
            $this->csrfTokenManager,
            $this->authService,
            $request,
        );
    }

    private function createRegisterRequest(): RegisterRequest
    {
        return new RegisterRequest(
            new Validator(new UserManager($this->getPdo())),
            $this->csrfTokenManager,
        );
    }

    private function createLoginRequest(): LoginRequest
    {
        return new LoginRequest(new Validator(), $this->csrfTokenManager);
    }

    private function getPdo(): \PDO
    {
        return DBManager::getInstance()->getPDO();
    }
}