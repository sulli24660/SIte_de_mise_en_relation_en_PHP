<?php

namespace App\Model;

use App\Service\DBManager;
use DateTime;

class BookManager
{
    public function findAll(): array
    {
        $pdo = DBManager::getInstance()->getPDO();
        $rows = $pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();

        $books = [];
        foreach ($rows as $row) {
            $books[] = $this->createBookFromRow($row);
        }

        return $books;
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