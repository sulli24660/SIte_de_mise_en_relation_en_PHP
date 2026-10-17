<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Service\Router;

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'books';

$router = new Router();
$router->dispatch($action, $_GET);