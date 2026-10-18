<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\BookManager;
use App\Exception\NotFoundException;

class BookController
{
    public function index(): void
    {
        $books = (new BookManager())->findAll();
        $this->render('books/index', ['books' => $books]);
    }

    public function show(int $id): void
    {
        $book = (new BookManager())->findById($id);

        if ($book === null) {
            throw new NotFoundException('Le livre avec l\'ID ' . $id . ' n\'existe pas.');
        }
        $this->render('books/show', ['book' => $book]);
    }
    private function render(string $view, array $data = []): void
    {
        extract($data);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}
