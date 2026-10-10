<?php
require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Controller\BookController;
use App\Core\RouterInterface;

$container = require dirname(__DIR__) . '/config/container.php';

$router = $container->get(RouterInterface::class);

$router->add('books', BookController::class, 'index');
$router->add('book', BookController::class, 'show');

$router
    ->dispatch($_GET['action'] ?? 'books')
    ->send();
