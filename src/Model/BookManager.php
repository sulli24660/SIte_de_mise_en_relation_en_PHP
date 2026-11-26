<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

final class BookManager extends AbstractManager
{
    protected function getTableName(): string
    {
        return 'books';
    }

    protected function getDefaultOrderBy(): string
    {
        return 'created_at DESC';
    }

    public function create(Book $book): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (user_id, title, author, image, description, is_available)
             VALUES (:user_id, :title, :author, :image, :description, :is_available)'
        );
        $stmt->execute([
            'user_id' => $book->getUserId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
            'description' => $book->getDescription(),
            'is_available' => (int) $book->isAvailable(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function createEntityFromRow(array $row): Book
    {
        $book = new Book();
        $book->setId((int) $row['id']);
        $book->setUserId((int) $row['user_id']);
        $book->setTitle($row['title']);
        $book->setAuthor($row['author']);
        $book->setImage($row['image']);
        $book->setDescription($row['description']);
        $book->setIsAvailable((bool) $row['is_available']);
        $book->setCreatedAt(new DateTime($row['created_at']));

        return $book;
    }
}