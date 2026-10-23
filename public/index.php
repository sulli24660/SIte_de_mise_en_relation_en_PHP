<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Service\Flash;
use App\Service\Router;
use App\Service\View;

session_start();

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'books';

try {
    (new Router())->dispatch($action, $_GET);
} catch (ValidationException $e) {
    Flash::put('errors', $e->errors->all());
    Flash::put('old', $e->old);
    // Retour au formulaire en GET (PRG). Le « / » unique empêche une redirection vers un autre domaine (« //evil.com »).
    header('Location: /' . ltrim($_SERVER['REQUEST_URI'] ?? '', '/'), true, 303);
} catch (NotFoundException $e) {
    http_response_code($e->getCode());
    (new View())->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log((string) $e);
    http_response_code(500);
    (new View())->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}
