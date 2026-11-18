<?php

declare(strict_types=1);

namespace App\Controller;

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

    public function login(): void
    {
        $errors = null;
        $email = '';

        if ($this->request->isPost()) {
            $email = mb_strtolower($this->request->post('email'));
            $password = $this->request->postRaw('password');
            $errors = new ErrorBag();

            if (!$this->hasValidCsrfToken()) {
                $errors->add('form', 'Votre session a expiré. Merci de renvoyer le formulaire.');
            } else {
                $user = $this->userService->authenticate($email, $password);

                if ($user === null) {
                    $errors->add('form', 'Identifiants incorrects.');
                } else {
                    $this->authService->login($user->getId());
                    $this->csrfTokenManager->regenerate();

                    header('Location: index.php?action=books');
                    exit;
                }
            }
        }

        $this->view->render('users/login', [
            'errors' => $errors,
            'email' => $email,
        ]);
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