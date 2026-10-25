<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Service\View;

final class BookController
{
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly View $view,
    ) {
    }

    public function index(): void
    {
        $books = $this->bookManager->findAll();

        $this->view->render('books/index', ['books' => $books]);
    }

    public function show(int $id): void
    {
        $book = $this->bookManager->findById($id);

        if ($book === null) {
            throw new NotFoundException('Le livre avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $this->view->render('books/show', ['book' => $book]);
    }
}