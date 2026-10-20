<?php

declare(strict_types=1);

namespace App\Validator;

class UserValidator
{
    public static function validatePseudo(string $pseudo): ?string
    {
        if ($pseudo === '') {
            return 'Le pseudo est requis.';
        }

        if (strlen($pseudo) < 3 || strlen($pseudo) > 50) {
            return 'Le pseudo doit contenir entre 3 et 50 caractères.';
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/D', $pseudo)) {
            return 'Le pseudo ne peut contenir que des lettres, des chiffres et des underscores.';
        }

        return null;
    }

    public static function validateEmail(string $email): ?string
    {
        if ($email === '') {
            return 'L\'adresse e-mail est requise.';
        }

        if (mb_strlen($email) > 255) {
            return 'L\'adresse email est trop longue.';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'L\'adresse e-mail n\'est pas valide.';
        }

        return null;
    }

    public static function validatePassword(string $password): ?string
    {
        if ($password === '') {
            return 'Le mot de passe est requis.';
        }

        if (strlen($password) < 8) {
            return 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if (strlen($password) > 72) {
            return 'Le mot de passe ne doit pas dépasser 72 caractères.';
        }

        return null;
    }
}
