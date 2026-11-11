<?php

declare(strict_types=1);

namespace App\Service;

final class CsrfTokenManager
{
    private const SESSION_KEY = 'csrf_token';

    public function getToken(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $this->regenerate();
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public function regenerate(): void
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
    }

    public function isValid(mixed $token): bool
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? null;

        // hash_equals compare en temps constant (protection contre les attaques temporelles).
        return is_string($token)
            && is_string($expected)
            && hash_equals($expected, $token);
    }
}
