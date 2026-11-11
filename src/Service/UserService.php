<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Model\UserManager;

final class UserService
{
    // Vérifié quand l'e-mail est inconnu : le temps de réponse ne révèle pas si un compte existe.
    private const DUMMY_PASSWORD_HASH = '$2y$10$fLAenOmNqh9NCbWm2ESTAuGgSPqH.AWmHjiP4IlZ1vQOH9Lhc2Uhq';

    public function __construct(private readonly UserManager $userManager)
    {
    }

    public function register(string $pseudo, string $email, string $password): int
    {
        $user = new User();
        $user->setPseudo($pseudo);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        return $this->userManager->create($user);
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userManager->findOneBy('email', $email);

        $passwordHash = $user?->getPassword() ?? self::DUMMY_PASSWORD_HASH;
        $isPasswordValid = password_verify($password, $passwordHash);

        return $user !== null && $isPasswordValid ? $user : null;
    }
}