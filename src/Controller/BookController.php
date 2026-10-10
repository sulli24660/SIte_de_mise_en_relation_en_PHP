<?php

namespace App\Controller;

use App\Core\Response;
use App\Exceptions\NotFoundException;
use App\Model\BookManager;
use Throwable;

class BookController
{
    public function index(): Response
    {
        $bookManager = new BookManager();
        $books = $bookManager->findAll();

        return $this->render('books/index', ['books' => $books]);
    }

    public function show(): Response
    {
        $id = (int) ($_GET['id'] ?? 0);
        $bookManager = new BookManager();
        $book = $bookManager->findById($id);

        if ($book === null) {
            throw new NotFoundException('Le livre avec l\'ID ' . $id . ' n\'existe pas.');
        }

        return $this->render('books/show', ['book' => $book]);
    }

    private function render(string $view, array $data = []): Response
    {
        extract($data);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        ob_start();
        try {
            require dirname(__DIR__, 2) . '/views/layout.php';
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return new Response(ob_get_clean());
    }
}
