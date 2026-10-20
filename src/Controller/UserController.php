<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\View;

class UserController
{
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pseudo = trim($this->getPostValue('pseudo'));
            $email = mb_strtolower(trim($this->getPostValue('email')));
            $password = $this->getPostValue('password');

            var_dump($pseudo, $email, $password);
        }

        (new View())->render('users/register');
    }

    private function getPostValue(string $key): string
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}