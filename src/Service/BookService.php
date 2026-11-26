<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
use App\Model\Book;
use App\Model\BookManager;

final class BookService
{
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly ImageUploader $imageUploader,
    ) {
    }

    public function addBook(int $userId, string $title, string $author, string $description, bool $isAvailable, ?array $image): int
    {
        $book = new Book();
        $book->setUserId($userId);
        $this->fillBook($book, $title, $author, $description, $isAvailable);

        if ($image !== null) {
            $book->setImage($this->imageUploader->store($image));
        }

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

    public function updateBook(Book $book, string $title, string $author, string $description, bool $isAvailable, ?array $image): void
    {
        $this->fillBook($book, $title, $author, $description, $isAvailable);
        $previousImage = $book->getImage();

        if ($image !== null) {
            $book->setImage($this->imageUploader->store($image));
        }

        $this->bookManager->update($book);

        if ($image !== null) {
            $this->imageUploader->delete($previousImage);
        }
    }

    public function deleteBook(Book $book): void
    {
        $this->bookManager->delete($book->getId());
        $this->imageUploader->delete($book->getImage());
    }

    private function fillBook(Book $book, string $title, string $author, string $description, bool $isAvailable): void
    {
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setDescription($description !== '' ? $description : null);
        $book->setIsAvailable($isAvailable);
    }
}
