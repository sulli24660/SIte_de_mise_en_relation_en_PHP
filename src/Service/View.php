<?php

declare(strict_types=1);

namespace App\Service;

use App\Request\Session;
use App\Validator\ErrorBag;

final class View
{
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
        private readonly Session $session,
    ) {
    }

    public function render(string $view, array $data = []): void
    {
        $data += [
            'errors' => new ErrorBag($this->session->pullFlash('errors', [])),
            'old' => $this->session->pullFlash('old', []),
            'success' => $this->session->pullFlash('success'),
        ];
        $data['isLoggedIn'] = $this->authService->isLoggedIn();
        $data['csrfToken'] = $this->csrfTokenManager->getToken();

        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}