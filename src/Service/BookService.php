<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
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
        $this->fillBook($book, $title, $author, $description, $isAvailable);

        return $this->bookManager->create($book);
    }

    // Même réponse que pour un livre inexistant : on ne révèle pas qu'un livre d'un autre membre existe.
    public function getOwnedBook(int $bookId, int $userId): Book
    {
        $book = $this->bookManager->findById($bookId);

        if (!$book instanceof Book || $book->getUserId() !== $userId) {
            throw new NotFoundException('Ce livre n\'existe pas.');
        }

        return $book;
    }

    public function updateBook(Book $book, string $title, string $author, string $description, bool $isAvailable): void
    {
        $this->fillBook($book, $title, $author, $description, $isAvailable);

        $this->bookManager->update($book);
    }

    public function deleteBook(Book $book): void
    {
        $this->bookManager->delete($book->getId());
    }

    private function fillBook(Book $book, string $title, string $author, string $description, bool $isAvailable): void
    {
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setDescription($description !== '' ? $description : null);
        $book->setIsAvailable($isAvailable);
    }
}
