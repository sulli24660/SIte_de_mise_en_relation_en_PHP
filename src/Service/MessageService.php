<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
use App\Model\Message;
use App\Model\MessageManager;
use App\Model\User;
use App\Model\UserManager;

final class MessageService
{
    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly UserManager $userManager,
    ) {
    }

    public function getPartner(int $partnerId, int $userId): User
    {
        $partner = $this->userManager->findById($partnerId);

        if (!$partner instanceof User || $partner->getId() === $userId) {
            throw new NotFoundException('Ce membre n\'existe pas.');
        }

        return $partner;
    }

    /**
     * @return list<Message>
     */
    public function openConversation(int $userId, int $partnerId): array
    {
        $this->messageManager->markConversationAsRead($userId, $partnerId);

        return $this->messageManager->findConversation($userId, $partnerId);
    }

    public function sendMessage(int $senderId, int $receiverId, string $content): void
    {
        $message = new Message();
        $message->setSenderId($senderId);
        $message->setReceiverId($receiverId);
        $message->setContent($content);

        $this->messageManager->create($message);
    }
}
