<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Request\BookRequest;
use App\Service\AuthService;
use App\Service\BookService;
use App\Service\Flash;
use App\Service\View;

final class BookController
{
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly BookService $bookService,
        private readonly AuthService $authService,
        private readonly Flash $flash,
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

    public function create(): void
    {
        $this->view->render('books/create');
    }

    public function store(BookRequest $bookRequest): void
    {
        $data = $bookRequest->validate();

        $this->bookService->addBook(
            $this->authService->getUserId() ?? 0,
            $data['title'],
            $data['author'],
            $data['description'],
            $data['is_available'] === '1',
        );

        $this->flash->put('success', 'Le livre a été ajouté à votre bibliothèque.');

        header('Location: index.php?action=account');
        exit;
    }
}
