<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\UserManager;
use App\Service\AuthService;
use App\Service\CsrfTokenManager;
use App\Service\View;
use App\Validator\ErrorBag;
use App\Validator\Validator;

final class UserController
{
    // Vérifié quand l'e-mail est inconnu : le temps de réponse ne révèle pas si un compte existe.
    private const DUMMY_PASSWORD_HASH = '$2y$10$fLAenOmNqh9NCbWm2ESTAuGgSPqH.AWmHjiP4IlZ1vQOH9Lhc2Uhq';

    public function __construct(
        private readonly UserManager $userManager,
        private readonly View $view,
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
    ) {}

    public function register(): void
    {
        $errors = null;
        $pseudo = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = trim($this->getPostValue('pseudo'));
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');

            if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
                $errors = new ErrorBag();
                $errors->add('form', 'Votre session a expiré. Merci de renvoyer le formulaire.');
            } else {
                $errors = $this->validateRegistration($pseudo, $email, $password);

                if ($errors->isEmpty()) {
                    $user = new User();
                    $user->setPseudo($pseudo);
                    $user->setEmail($email);
                    $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

                    $this->userManager->create($user);

                    header('Location: index.php?action=books');
                    exit;
                }
            }
        }

        $this->view->render('users/register', [
            'errors' => $errors,
            'pseudo' => $pseudo,
            'email' => $email,
        ]);
    }

    public function login(): void
    {
        $errors = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');
            $errors = new ErrorBag();

            if (!$this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)) {
                $errors->add('form', 'Votre session a expiré. Merci de renvoyer le formulaire.');
            } else {
                $user = $this->userManager->findByEmail($email);

                $passwordHash = $user?->getPassword() ?? self::DUMMY_PASSWORD_HASH;
                $isPasswordValid = password_verify($password, $passwordHash);

                if ($user === null || !$isPasswordValid) {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && $this->csrfTokenManager->isValid($_POST['csrf_token'] ?? null)
        ) {
            $this->authService->logout();
        }

        header('Location: index.php?action=books');
        exit;
    }

    private function validateRegistration(string $pseudo, string $email, string $password): ErrorBag
    {
        $errors = Validator::validate(
            [
                'pseudo' => $pseudo,
                'email' => $email,
                'password' => $password,
            ],
            [
                'pseudo' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/D'],
                'email' => ['required', 'max:255', 'email'],
                // bcrypt ignore tout ce qui dépasse 72 octets.
                'password' => ['required', 'min:8', 'maxbytes:72'],
            ],
            [
                'pseudo.regex' => 'Le pseudo ne peut contenir que des lettres, des chiffres et des underscores.',
            ]
        );

        if (!$errors->has('pseudo') && $this->userManager->pseudoExists($pseudo)) {
            $errors->add('pseudo', 'Ce pseudo est déjà pris.');
        }

        if (!$errors->has('email') && $this->userManager->emailExists($email)) {
            $errors->add('email', 'Cette adresse e-mail est déjà utilisée.');
        }

        return $errors;
    }

    private function getPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}
