<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\UserManager;
use App\Service\View;
use App\Validator\UserValidator;

class UserController
{
    public function register(): void
    {

        $errors = [];
        $pseudo = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = trim($this->getPostValue('pseudo'));
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');

            $pseudoError = UserValidator::validatePseudo($pseudo);
            if ($pseudoError !== null) {
                $errors['pseudo'] = $pseudoError;
            }

            $emailError = UserValidator::validateEmail($email);
            if ($emailError !== null) {
                $errors['email'] = $emailError;
            }

            $passwordError = UserValidator::validatePassword($password);
            if ($passwordError !== null) {
                $errors['password'] = $passwordError;
            }

            $userManager = new UserManager();

            if (!isset($errors['pseudo']) && $userManager->pseudoExists($pseudo)) {
                $errors['pseudo'] = 'Ce pseudo est déjà pris.';
            }

            if (!isset($errors['email']) && $userManager->emailExists($email)) {
                $errors['email'] = 'Cette adresse email est déjà utilisée.';
            }
        }

        (new View())->render('users/register', [
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
