<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ValidationException;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\Request;
use App\Service\AuthService;
use App\Service\CsrfTokenManager;
use App\Service\UserService;
use App\Service\View;
use App\Validator\ErrorBag;

final class UserController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly View $view,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
        private readonly Request $request,
        private readonly ErrorBag $errorBag
    ) {}

    public function register(RegisterRequest $registerRequest): void
    {
        if ($registerRequest->isPost()) {
            $data = $registerRequest->validate();

            $this->userService->register($data['pseudo'], $data['email'], $data['password']);

            header('Location: index.php?action=books');
            exit;
        }

        $this->view->render('users/register');
    }

    public function login(LoginRequest $loginRequest): void
    {
        if ($loginRequest->isPost()) {
            $data = $loginRequest->validate();

            $user = $this->userService->authenticate($data['email'], $data['password']);

            if ($user === null) {
                throw new ValidationException(
                    new ErrorBag(['form' => 'Identifiants incorrects.']),
                    ['email' => $data['email']],
                );
            }

            $this->authService->login($user->getId());
            $this->csrfTokenManager->regenerate();

            header('Location: index.php?action=books');
            exit;
        }

        $this->view->render('users/login');
    }

    public function logout(): void
    {
        if ($this->request->isPost() && $this->hasValidCsrfToken()) {
            $this->authService->logout();
        }

        header('Location: index.php?action=books');
        exit;
    }

    private function hasValidCsrfToken(): bool
    {
        return $this->csrfTokenManager->isValid($this->request->post('csrf_token'));
    }
}
