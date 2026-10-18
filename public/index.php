<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Service\Router;
use app\Exception\NotFoundException;
use App\Service\View;

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'books';

try {
    (new Router())->dispatch($action, $_GET);
} catch (NotFoundException $e) {
    http_response_code($e->getCode());
    (new View())->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log((string) $e);
    http_response_code(500);
    (new View())->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}
