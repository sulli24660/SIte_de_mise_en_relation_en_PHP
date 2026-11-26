<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use PDO;

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

    public function findForCatalog(string $titleSearch = '', ?int $limit = null): array
    {
        $sql = 'SELECT books.*, users.pseudo AS owner_pseudo
                FROM books
                INNER JOIN users ON users.id = books.user_id';

        if ($titleSearch !== '') {
            // « ! » sert de caractère d'échappement : % et _ saisis par l'utilisateur sont cherchés tels quels.
            $sql .= " WHERE books.title LIKE :pattern ESCAPE '!'";
        }

        $sql .= ' ORDER BY books.created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT :limit';
        }

        $stmt = $this->pdo->prepare($sql);

        if ($titleSearch !== '') {
            $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $titleSearch);
            $stmt->bindValue('pattern', '%' . $escaped . '%');
        }

        if ($limit !== null) {
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();

        return array_map(fn (array $row): Book => $this->createEntityFromRow($row), $stmt->fetchAll());
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

    public function update(Book $book): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books
             SET title = :title, author = :author, image = :image, description = :description, is_available = :is_available
             WHERE id = :id'
        );
        $stmt->execute([
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'image' => $book->getImage(),
            'description' => $book->getDescription(),
            'is_available' => (int) $book->isAvailable(),
            'id' => $book->getId(),
        ]);
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
        $book->setOwnerPseudo($row['owner_pseudo'] ?? null);

        return $book;
    }
}