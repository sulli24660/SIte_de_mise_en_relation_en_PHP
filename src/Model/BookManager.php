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

    protected function getSearchableColumns(): array
    {
        return ['id', 'user_id'];
    }

    protected function getDefaultOrderBy(): string
    {
        return 'created_at DESC';
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