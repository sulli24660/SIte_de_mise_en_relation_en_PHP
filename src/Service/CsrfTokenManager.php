<?php

declare(strict_types=1);

namespace App\Service;

use App\Request\Session;

final class CsrfTokenManager
{
    private const SESSION_KEY = 'csrf_token';

    public function __construct(private readonly Session $session)
    {
    }

    public function getToken(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if (!is_string($token)) {
            $this->regenerate();
            $token = $this->session->get(self::SESSION_KEY);
        }

        return $token;
    }

    public function regenerate(): void
    {
        $this->session->set(self::SESSION_KEY, bin2hex(random_bytes(32)));
    }

    public function isValid(mixed $token): bool
    {
        $expected = $this->session->get(self::SESSION_KEY);

        // hash_equals compare en temps constant (protection contre les attaques temporelles).
        return is_string($token)
            && is_string($expected)
            && hash_equals($expected, $token);
    }
}
