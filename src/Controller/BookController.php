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
        $this->view->render('books/create', ['book' => null]);
    }

    public function store(BookRequest $bookRequest): void
    {
        $data = $bookRequest->validate();

        $this->bookService->addBook(
            $this->getCurrentUserId(),
            $data['title'],
            $data['author'],
            $data['description'],
            $data['is_available'] === '1',
        );

        $this->redirectToAccount('Le livre a été ajouté à votre bibliothèque.');
    }

    public function edit(int $id): void
    {
        $book = $this->bookService->getOwnedBook($id, $this->getCurrentUserId());

        $this->view->render('books/edit', ['book' => $book]);
    }

    public function update(int $id, BookRequest $bookRequest): void
    {
        $book = $this->bookService->getOwnedBook($id, $this->getCurrentUserId());
        $data = $bookRequest->validate();

        $this->bookService->updateBook(
            $book,
            $data['title'],
            $data['author'],
            $data['description'],
            $data['is_available'] === '1',
        );

        $this->redirectToAccount('Le livre a été modifié.');
    }

    private function getCurrentUserId(): int
    {
        return $this->authService->getUserId() ?? 0;
    }

    private function redirectToAccount(string $successMessage): never
    {
        $this->flash->put('success', $successMessage);

        header('Location: index.php?action=account');
        exit;
    }
}
