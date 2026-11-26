<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Request\BookRequest;
use App\Request\DeleteBookRequest;
use App\Service\AuthService;
use App\Service\BookService;
use App\Service\Flash;
use App\Service\View;

final class BookController
{
    public function __construct(
        private readonly BookManager $bookManager,
        private readonly UserManager $userManager,
        private readonly BookService $bookService,
        private readonly AuthService $authService,
        private readonly Flash $flash,
        private readonly View $view,
    ) {
    }

    public function index(string $search): void
    {
        $this->view->render('books/index', [
            'books' => $this->bookManager->findForCatalog($search),
            'search' => $search,
        ]);
    }

    public function show(int $id): void
    {
        $book = $this->bookManager->findById($id);

        if ($book === null) {
            throw new NotFoundException('Le livre avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $this->view->render('books/show', [
            'book' => $book,
            'owner' => $this->userManager->findById($book->getUserId()),
            'isOwnBook' => $book->getUserId() === $this->authService->getUserId(),
        ]);
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
            $bookRequest->image(),
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
            $bookRequest->image(),
        );

        $this->redirectToAccount('Le livre a été modifié.');
    }

    public function delete(int $id, DeleteBookRequest $deleteBookRequest): void
    {
        if (!$deleteBookRequest->isPost()) {
            $this->redirectToAccount();
        }

        $book = $this->bookService->getOwnedBook($id, $this->getCurrentUserId());
        $deleteBookRequest->validate();

        $this->bookService->deleteBook($book);

        $this->redirectToAccount('Le livre a été supprimé.');
    }

    private function getCurrentUserId(): int
    {
        return $this->authService->getUserId() ?? 0;
    }

    private function redirectToAccount(?string $successMessage = null): never
    {
        if ($successMessage !== null) {
            $this->flash->put('success', $successMessage);
        }

        header('Location: index.php?action=account');
        exit;
    }
}
