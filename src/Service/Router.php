<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\AccountController;
use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Middleware\AuthMiddleware;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\Request;
use App\Request\UpdateAccountRequest;

final class Router
{
    private const PROTECTED_ACTIONS = ['account', 'messages'];

    public function __construct(
        private readonly Container $container
    )
    {
    }

    public function dispatch(Request $request): void
    {
        $action = $request->query('action', 'books');

        if (in_array($action, self::PROTECTED_ACTIONS, true)) {
            $this->container->get(AuthMiddleware::class)->handle();
        }

        switch ($action) {
            case 'books':
                $this->container->get(BookController::class)->index();
                break;

            case 'book':
                $this->container->get(BookController::class)->show($request->queryInt('id'));
                break;

            case 'register':
                $this->container->get(UserController::class)->register($this->container->get(RegisterRequest::class));
                break;

            case 'login':
                $this->container->get(UserController::class)->login($this->container->get(LoginRequest::class));
                break;

            case 'logout':
                $this->container->get(UserController::class)->logout();
                break;

            case 'account':
                $request->isPost()
                    ? $this->container->get(AccountController::class)->update($this->container->get(UpdateAccountRequest::class))
                    : $this->container->get(AccountController::class)->show();
                break;

            default:
                throw new NotFoundException();
        }
    }
}
