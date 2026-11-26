<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Service\AuthService;
use App\Service\View;

final class ProfileController
{
    public function __construct(
        private readonly UserManager $userManager,
        private readonly BookManager $bookManager,
        private readonly AuthService $authService,
        private readonly View $view,
    ) {
    }

    public function show(int $id): void
    {
        $member = $this->userManager->findById($id);

        if ($member === null) {
            throw new NotFoundException('Ce membre n\'existe pas.');
        }

        $this->view->render('profile/show', [
            'member' => $member,
            'books' => $this->bookManager->findAllBy('user_id', $member->getId()),
            'isOwnProfile' => $member->getId() === $this->authService->getUserId(),
        ]);
    }
}
