<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Request\Request;
use App\Request\Session;
use App\Service\Container;
use App\Service\Router;
use App\Service\View;

/** @var Container $container */
$container = require dirname(__DIR__) . '/config/container.php';

$session = $container->get(Session::class);
$session->start();

$request = $container->get(Request::class);
$view = $container->get(View::class);

try {
    $container->get(Router::class)->dispatch($request);
} catch (ValidationException $e) {
    $session->flash('errors', $e->errors->all());
    $session->flash('old', $e->old);
    header('Location: index.php?action=' . rawurlencode($request->query('action')), true, 303);
    exit;
} catch (NotFoundException $e) {
    http_response_code($e->getCode());
    $view->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    // Le détail technique va dans le journal du serveur, jamais à l'écran.
    error_log((string) $e);
    http_response_code(500);
    $view->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}