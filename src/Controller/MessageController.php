<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MessageManager;
use App\Request\SendMessageRequest;
use App\Service\AuthService;
use App\Service\MessageService;
use App\Service\View;

final class MessageController
{
    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly MessageService $messageService,
        private readonly AuthService $authService,
        private readonly View $view,
    ) {
    }

    public function index(int $partnerId): void
    {
        $userId = $this->getCurrentUserId();
        $partner = null;
        $messages = [];

        if ($partnerId !== 0) {
            $partner = $this->messageService->getPartner($partnerId, $userId);
            $messages = $this->messageService->openConversation($userId, $partnerId);
        }

        $this->view->render('messages/index', [
            'conversations' => $this->messageManager->findConversationSummaries($userId),
            'partner' => $partner,
            'messages' => $messages,
            'userId' => $userId,
        ]);
    }

    public function send(int $partnerId, SendMessageRequest $sendMessageRequest): void
    {
        $userId = $this->getCurrentUserId();
        $partner = $this->messageService->getPartner($partnerId, $userId);
        $data = $sendMessageRequest->validate();

        $this->messageService->sendMessage($userId, $partner->getId(), $data['content']);

        header('Location: index.php?action=messages&with=' . $partner->getId());
        exit;
    }

    private function getCurrentUserId(): int
    {
        return $this->authService->getUserId() ?? 0;
    }
}
