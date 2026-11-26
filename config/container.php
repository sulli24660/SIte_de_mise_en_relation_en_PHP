<?php

declare(strict_types=1);

use App\Controller\AccountController;
use App\Controller\BookController;
use App\Controller\UserController;
use App\Core\Container;
use App\Middleware\AuthMiddleware;
use App\Model\BookManager;
use App\Model\UserManager;
use App\Request\BookRequest;
use App\Request\DeleteBookRequest;
use App\Request\LoginRequest;
use App\Request\RegisterRequest;
use App\Request\Request;
use App\Request\UpdateAccountRequest;
use App\Service\AuthService;
use App\Service\BookService;
use App\Service\CsrfTokenManager;
use App\Service\DBManager;
use App\Service\Flash;
use App\Service\ImageUploader;
use App\Service\Router;
use App\Service\UserService;
use App\Service\View;
use App\Validator\Validator;

$container = new Container();

// Services partagés
$container->set(PDO::class, fn () => DBManager::getInstance()->getPDO());
$container->set(Request::class, fn () => new Request());
$container->set(Flash::class, fn () => new Flash());
$container->set(CsrfTokenManager::class, fn () => new CsrfTokenManager());
$container->set(AuthService::class, fn () => new AuthService());
$container->set(View::class, fn (Container $c) => new View(
    $c->get(CsrfTokenManager::class),
    $c->get(AuthService::class),
    $c->get(Flash::class),
));
$container->set(Router::class, fn (Container $c) => new Router($c));
$container->set(AuthMiddleware::class, fn (Container $c) => new AuthMiddleware($c->get(AuthService::class)));

// Modèle et métier
$container->set(UserManager::class, fn (Container $c) => new UserManager($c->get(PDO::class)));
$container->set(BookManager::class, fn (Container $c) => new BookManager($c->get(PDO::class)));
$container->set(UserService::class, fn (Container $c) => new UserService($c->get(UserManager::class)));
$container->set(ImageUploader::class, fn () => new ImageUploader(dirname(__DIR__) . '/public/uploads/books'));
$container->set(BookService::class, fn (Container $c) => new BookService(
    $c->get(BookManager::class),
    $c->get(ImageUploader::class),
));

// Formulaires
$container->set(RegisterRequest::class, fn (Container $c) => new RegisterRequest(
    new Validator($c->get(UserManager::class)),
    $c->get(CsrfTokenManager::class),
));
$container->set(LoginRequest::class, fn (Container $c) => new LoginRequest(
    new Validator(),
    $c->get(CsrfTokenManager::class),
));
$container->set(UpdateAccountRequest::class, fn (Container $c) => new UpdateAccountRequest(
    new Validator($c->get(UserManager::class), $c->get(AuthService::class)->getUserId()),
    $c->get(CsrfTokenManager::class),
));

$container->set(BookRequest::class, fn (Container $c) => new BookRequest(
    new Validator(),
    $c->get(CsrfTokenManager::class),
    $c->get(ImageUploader::class),
));
$container->set(DeleteBookRequest::class, fn (Container $c) => new DeleteBookRequest(
    new Validator(),
    $c->get(CsrfTokenManager::class),
));

// Contrôleurs
$container->set(BookController::class, fn (Container $c) => new BookController(
    $c->get(BookManager::class),
    $c->get(BookService::class),
    $c->get(AuthService::class),
    $c->get(Flash::class),
    $c->get(View::class),
));
$container->set(UserController::class, fn (Container $c) => new UserController(
    $c->get(UserService::class),
    $c->get(View::class),
    $c->get(CsrfTokenManager::class),
    $c->get(AuthService::class),
    $c->get(Request::class),
));
$container->set(AccountController::class, fn (Container $c) => new AccountController(
    $c->get(UserManager::class),
    $c->get(BookManager::class),
    $c->get(UserService::class),
    $c->get(AuthService::class),
    $c->get(Flash::class),
    $c->get(View::class),
));

return $container;
