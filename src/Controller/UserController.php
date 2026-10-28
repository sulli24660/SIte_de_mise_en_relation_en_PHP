<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\UserManager;
use App\Service\View;
use App\Validator\Validator;

final class UserController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly View $view,
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

            $errors = Validator::validate(
                [
                    'pseudo' => $pseudo,
                    'email' => $email,
                    'password' => $password,
                ],
                [
                    'pseudo' => ['required', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9_]+$/D'],
                    'email' => ['required', 'max:255', 'email'],
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

        $this->view->render('users/register', [
            'errors' => $errors,
            'pseudo' => $pseudo,
            'email' => $email,
        ]);
    }

    private function getPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}