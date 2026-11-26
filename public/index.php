<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Request\Request;
use App\Service\Flash;
use App\Service\Router;
use App\Service\View;

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    // Actif seulement en HTTPS, sinon la session ne fonctionnerait pas en local (php -S).
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'use_strict_mode' => true,
]);

$container = require dirname(__DIR__) . '/config/container.php';
$request = $container->get(Request::class);

try {
    $container->get(Router::class)->dispatch($request);
} catch (ValidationException $e) {
    $flash = $container->get(Flash::class);
    $flash->put('errors', $e->errors->all());
    $flash->put('old', $e->old);
    header('Location: index.php?' . $request->queryString(), true, 303);
    exit;
} catch (NotFoundException $e) {
    http_response_code($e->getCode());
    $container->get(View::class)->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran.
    error_log((string) $e);
    http_response_code(500);
    $container->get(View::class)->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}
