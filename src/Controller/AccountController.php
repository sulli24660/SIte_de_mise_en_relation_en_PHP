<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Service\AuthService;
use App\Service\View;

final class AccountController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly BookManager $bookManager,
        private readonly AuthService $authService,
        private readonly View $view,
    ) {
    }

    public function show(): void
    {
        $user = $this->userManager->findById($this->authService->getUserId() ?? 0);

        if ($user === null) {
            throw new NotFoundException('Ce compte n\'existe plus.');
        }

        $this->view->render('account/show', [
            'user' => $user,
            'books' => $this->bookManager->findAllBy('user_id', $user->getId()),
        ]);
    }
}
