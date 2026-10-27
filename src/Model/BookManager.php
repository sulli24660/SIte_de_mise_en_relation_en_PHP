<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use PDO;

final class BookManager
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findAll(): array
    {
        $rows = $this->pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();

        $books = [];
        foreach ($rows as $row) {
            $books[] = $this->createBookFromRow($row);
        }

        return $books;
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->createBookFromRow($row);
    }

    private function createBookFromRow(array $row): Book
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