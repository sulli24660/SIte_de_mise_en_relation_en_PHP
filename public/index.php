<?php
require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Controller\BookController;

$action = $_GET['action'] ?? 'books';

$bookController = new BookController();

switch ($action) {
    case 'books':
        $bookController->index();
        break;
    case 'book':
        $controller = new BookController();
        $controller->show((int) ($_GET['id'] ?? 0));
        break;
    default:
        http_response_code(404);
        echo 'Page introuvable';
        break;
}
