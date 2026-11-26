<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

final class MessageManager extends AbstractManager
{
    protected function getTableName(): string
    {
        return 'messages';
    }

    public function create(Message $message): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO messages (sender_id, receiver_id, content) VALUES (:sender_id, :receiver_id, :content)'
        );
        $stmt->execute([
            'sender_id' => $message->getSenderId(),
            'receiver_id' => $message->getReceiverId(),
            'content' => $message->getContent(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return list<Message>
     */
    public function findConversation(int $userId, int $partnerId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM messages
             WHERE (sender_id = :me1 AND receiver_id = :partner1)
                OR (sender_id = :partner2 AND receiver_id = :me2)
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute(['me1' => $userId, 'partner1' => $partnerId, 'partner2' => $partnerId, 'me2' => $userId]);

        return array_map(fn (array $row): Message => $this->createEntityFromRow($row), $stmt->fetchAll());
    }

    /**
     * Une ligne par interlocuteur : son dernier message et le nombre de messages non lus.
     *
     * @return list<ConversationSummary>
     */
    public function findConversationSummaries(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT users.id AS partner_id, users.pseudo AS partner_pseudo,
                    messages.content AS last_message, messages.created_at AS last_message_at,
                    (SELECT COUNT(*) FROM messages AS unread
                     WHERE unread.sender_id = users.id AND unread.receiver_id = :me1 AND unread.is_read = 0) AS unread_count
             FROM messages
             INNER JOIN users
                ON users.id = CASE WHEN messages.sender_id = :me2 THEN messages.receiver_id ELSE messages.sender_id END
             WHERE messages.id IN (
                SELECT MAX(id) FROM messages
                WHERE sender_id = :me3 OR receiver_id = :me4
                GROUP BY CASE WHEN sender_id = :me5 THEN receiver_id ELSE sender_id END
             )
             ORDER BY messages.created_at DESC, messages.id DESC'
        );
        $stmt->execute(['me1' => $userId, 'me2' => $userId, 'me3' => $userId, 'me4' => $userId, 'me5' => $userId]);

        return array_map(
            fn (array $row): ConversationSummary => new ConversationSummary(
                (int) $row['partner_id'],
                $row['partner_pseudo'],
                $row['last_message'],
                new DateTime($row['last_message_at']),
                (int) $row['unread_count'],
            ),
            $stmt->fetchAll()
        );
    }

    public function markConversationAsRead(int $userId, int $partnerId): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE messages SET is_read = 1 WHERE sender_id = :partner AND receiver_id = :me AND is_read = 0'
        );
        $stmt->execute(['partner' => $partnerId, 'me' => $userId]);
    }

    public function countUnreadFor(int $userId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM messages WHERE receiver_id = :me AND is_read = 0');
        $stmt->execute(['me' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    protected function createEntityFromRow(array $row): Message
    {
        $message = new Message();
        $message->setId((int) $row['id']);
        $message->setSenderId((int) $row['sender_id']);
        $message->setReceiverId((int) $row['receiver_id']);
        $message->setContent($row['content']);
        $message->setIsRead((bool) $row['is_read']);
        $message->setCreatedAt(new DateTime($row['created_at']));

        return $message;
    }
}
