<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use PDO;

/**
 * Accès aux données de la table "books".
 *
 * Ce manager ne contient que des requêtes SQL et la transformation
 * des lignes de la base en objets Book.
 */
final class BookManager
{
    /**
     * @param PDO $pdo Connexion à la base de données, fournie par le Router.
     */
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retourne tous les livres, du plus récent au plus ancien.
     *
     * @return list<Book> Liste des livres (vide s'il n'y en a aucun).
     */
    public function findAll(): array
    {
        // Requête sans paramètre venant de l'utilisateur : query() suffit ici.
        $rows = $this->pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll();

        $books = [];
        foreach ($rows as $row) {
            $books[] = $this->createBookFromRow($row);
        }

        return $books;
    }

    /**
     * Retrouve un livre à partir de son identifiant.
     *
     * @param int $id Identifiant du livre recherché.
     *
     * @return Book|null Le livre, ou null si aucun livre ne correspond.
     */
    public function findById(int $id): ?Book
    {
        // Requête préparée : l'id est transmis séparément du SQL (anti-injection).
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->createBookFromRow($row);
    }

    /**
     * Transforme une ligne de la table "books" en objet Book.
     *
     * @param array<string, mixed> $row Ligne retournée par PDO.
     *
     * @return Book Livre construit à partir de la ligne.
     */
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
