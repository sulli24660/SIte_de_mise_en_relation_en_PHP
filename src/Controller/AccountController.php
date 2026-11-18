<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Request\UpdateAccountRequest;
use App\Service\AuthService;
use App\Service\Flash;
use App\Service\UserService;
use App\Service\View;

final class AccountController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly BookManager $bookManager,
        private readonly UserService $userService,
        private readonly AuthService $authService,
        private readonly Flash $flash,
        private readonly View $view,
    ) {
    }

    public function show(): void
    {
        $user = $this->userManager->findById($this->getCurrentUserId());

        if ($user === null) {
            throw new NotFoundException('Ce compte n\'existe plus.');
        }

        $this->view->render('account/show', [
            'user' => $user,
            'books' => $this->bookManager->findAllBy('user_id', $user->getId()),
        ]);
    }

    public function update(UpdateAccountRequest $updateAccountRequest): void
    {
        $data = $updateAccountRequest->validate();

        $this->userService->updateAccount(
            $this->getCurrentUserId(),
            $data['pseudo'],
            $data['email'],
            $data['password'],
        );

        $this->flash->put('success', 'Vos informations ont été mises à jour.');

        header('Location: index.php?action=account');
        exit;
    }

    private function getCurrentUserId(): int
    {
        return $this->authService->getUserId() ?? 0;
    }
}
