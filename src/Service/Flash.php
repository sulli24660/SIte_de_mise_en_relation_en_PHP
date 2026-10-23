<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Données de session lisibles une seule fois, à la requête suivante (après une redirection).
 */
final class Flash
{
    private const SESSION_KEY = '_flash';

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[self::SESSION_KEY][$key] = $value;
    }

    public static function pull(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[self::SESSION_KEY][$key] ?? $default;
        unset($_SESSION[self::SESSION_KEY][$key]);

        return $value;
    }
}
