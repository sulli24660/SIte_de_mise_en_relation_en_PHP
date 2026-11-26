<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

final class ConversationSummary
{
    public function __construct(
        public readonly int $partnerId,
        public readonly string $partnerPseudo,
        public readonly string $lastMessage,
        public readonly DateTime $lastMessageAt,
        public readonly int $unreadCount,
    ) {
    }
}
