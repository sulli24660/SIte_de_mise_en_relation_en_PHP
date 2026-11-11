<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Exception\NotFoundException;
use App\Request\Request;
use App\Service\AuthService;
use App\Service\CsrfTokenManager;
use App\Service\Router;
use App\Service\View;

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    // Actif seulement en HTTPS, sinon la session ne fonctionnerait pas en local (php -S).
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'use_strict_mode' => true,
]);

try {
    (new Router())->dispatch(new Request());
} catch (NotFoundException $e) {
    http_response_code($e->getCode());
    (new View(new CsrfTokenManager(), new AuthService()))->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran.
    error_log((string) $e);
    http_response_code(500);
    (new View(new CsrfTokenManager(), new AuthService()))->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}