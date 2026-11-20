<?php

declare(strict_types=1);

namespace App\Service;

use App\Request\Session;

final class AuthService
{
    private const SESSION_KEY = 'user_id';

    public function __construct(private readonly Session $session)
    {
    }

    public function isLoggedIn(): bool
    {
        return $this->getUserId() !== null;
    }

    public function getUserId(): ?int
    {
        $userId = $this->session->get(self::SESSION_KEY);

        return is_int($userId) ? $userId : null;
    }

    public function login(int $userId): void
    {
        // Nouvel identifiant de session à la connexion : protège contre la fixation de session.
        $this->session->regenerate();

        $this->session->set(self::SESSION_KEY, $userId);
    }

    public function logout(): void
    {
        $this->session->destroy();
    }
}
