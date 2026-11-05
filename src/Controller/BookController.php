<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Service\View;

/**
 * Gère les pages publiques liées aux livres (liste et détail).
 */
final class BookController
{
    /**
     * @param BookManager $bookManager Accès aux livres en base de données.
     * @param View        $view        Service d'affichage des vues.
     */
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly View $view,
    ) {
    }

    /**
     * Affiche la liste de tous les livres, du plus récent au plus ancien.
     */
    public function index(): void
    {
        $books = $this->bookManager->findAll();

        $this->view->render('books/index', ['books' => $books]);
    }

    /**
     * Affiche la page de détail d'un livre.
     *
     * @param int $id Identifiant du livre (0 si l'id de l'URL n'est pas numérique).
     *
     * @throws NotFoundException Si aucun livre ne correspond à cet identifiant.
     */
    public function show(int $id): void
    {
        $book = $this->bookManager->findById($id);

        if ($book === null) {
            throw new NotFoundException('Le livre avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $this->view->render('books/show', ['book' => $book]);
    }
}
