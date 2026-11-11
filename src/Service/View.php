<?php

declare(strict_types=1);

namespace App\Service;

final class View
{
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
    ) {
    }

    public function render(string $view, array $data = []): void
    {
        $data['isLoggedIn'] = $this->authService->isLoggedIn();
        $data['csrfToken'] = $this->csrfTokenManager->getToken();

        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}
