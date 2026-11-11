<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\AuthService;

final class AuthMiddleware
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function handle(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: index.php?action=login');
            exit;
        }
    }
}
