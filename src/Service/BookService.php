<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Book;
use App\Model\BookManager;

final class BookService
{
    public function __construct(private readonly BookManager $bookManager)
    {
    }

    public function addBook(int $userId, string $title, string $author, string $description, bool $isAvailable): int
    {
        $book = new Book();
        $book->setUserId($userId);
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setDescription($description !== '' ? $description : null);
        $book->setIsAvailable($isAvailable);

        return $this->bookManager->create($book);
    }
}
