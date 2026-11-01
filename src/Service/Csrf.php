<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Jeton anti-CSRF stocké en session, à placer dans un champ caché de chaque formulaire POST.
 */
final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public function token(): string
    {
        return $_SESSION[self::SESSION_KEY] ??= bin2hex(random_bytes(32));
    }

    public function isValid(string $token): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
