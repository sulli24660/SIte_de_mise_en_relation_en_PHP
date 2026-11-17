<?php

declare(strict_types=1);

namespace App\Service;

use App\Validator\ErrorBag;

final class View
{
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
        private readonly Flash $flash,
    ) {
    }

    public function render(string $view, array $data = []): void
    {
        $data += [
            'errors' => new ErrorBag($this->flash->pull('errors', [])),
            'old' => $this->flash->pull('old', []),
        ];
        $data['isLoggedIn'] = $this->authService->isLoggedIn();
        $data['csrfToken'] = $this->csrfTokenManager->getToken();

        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}