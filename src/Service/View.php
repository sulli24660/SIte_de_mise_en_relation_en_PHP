<?php

declare(strict_types=1);

namespace App\Service;

use App\Validator\ErrorBag;
use Closure;
use Throwable;

final class View
{
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
        private readonly Flash $flash,
        private readonly Closure $countUnreadMessages,
    ) {
    }

    public function render(string $view, array $data = []): void
    {
        $data += [
            'errors' => new ErrorBag($this->flash->pull('errors', [])),
            'old' => $this->flash->pull('old', []),
            'success' => $this->flash->pull('success'),
        ];
        $data['isLoggedIn'] = $this->authService->isLoggedIn();
        $data['csrfToken'] = $this->csrfTokenManager->getToken();
        $data['unreadMessagesCount'] = $this->countUnreadMessagesSafely();

        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }

    // Information secondaire : si la base est indisponible (page d'erreur 500), on affiche simplement 0.
    private function countUnreadMessagesSafely(): int
    {
        $userId = $this->authService->getUserId();

        if ($userId === null) {
            return 0;
        }

        try {
            return ($this->countUnreadMessages)($userId);
        } catch (Throwable) {
            return 0;
        }
    }
}