<?php

namespace App\Controller;

use App\Model\BookManager;

class BookController
{
    public function index(): void
    {
        $bookManager = new BookManager();
        $books = $bookManager->findAll();

        $this->render('books/index', ['books' => $books]);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }

    public function show(int $id): void
    {
        $bookManager = new BookManager();
        $book = $bookManager->findById($id);

        if ($book === null) {
            http_response_code(404);
            echo 'Le livre avec l\'ID ' . $id . ' n\'existe pas.';
            return;
        }

        $this->render('books/show', ['book' => $book]);
    }
}
