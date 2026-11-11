<?php

declare(strict_types=1);

namespace App\Service;

final class AuthService
{
    private const SESSION_KEY = 'user_id';

    public function isLoggedIn(): bool
    {
        return $this->getUserId() !== null;
    }

    public function getUserId(): ?int
    {
        $userId = $_SESSION[self::SESSION_KEY] ?? null;

        return is_int($userId) ? $userId : null;
    }

    public function login(int $userId): void
    {
        // Nouvel identifiant de session à la connexion : protège contre la fixation de session.
        session_regenerate_id(true);

        $_SESSION[self::SESSION_KEY] = $userId;
    }

    public function logout(): void
    {
        $_SESSION = [];

        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);

        session_destroy();
    }
}
