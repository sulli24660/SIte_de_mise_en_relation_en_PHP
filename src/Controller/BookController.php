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
}
