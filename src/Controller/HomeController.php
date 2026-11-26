<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\BookManager;
use App\Service\View;

final class HomeController
{
    private const LATEST_BOOKS_COUNT = 4;

    public function __construct(
        private readonly BookManager $bookManager,
        private readonly View $view,
    ) {
    }

    public function index(): void
    {
        $this->view->render('home/index', [
            'latestBooks' => $this->bookManager->findForCatalog(limit: self::LATEST_BOOKS_COUNT),
        ]);
    }
}
