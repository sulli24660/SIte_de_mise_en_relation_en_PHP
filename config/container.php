<?php

declare(strict_types=1);

use App\Model\UserManager;
use App\Request\RegisterRequest;
use App\Request\UpdateAccountRequest;
use App\Service\AuthService;
use App\Service\Container;
use App\Service\CsrfTokenManager;
use App\Service\DBManager;
use App\Validator\Validator;

// Seules les dépendances que l'autowiring ne peut pas deviner sont déclarées ici.
$container = new Container();

$container->set(PDO::class, fn (): PDO => DBManager::getInstance()->getPDO());

// Le Validator change selon le formulaire : vérification d'unicité, utilisateur à exclure.
$container->set(RegisterRequest::class, fn (Container $c): RegisterRequest => new RegisterRequest(
    new Validator($c->get(UserManager::class)),
    $c->get(CsrfTokenManager::class),
));

$container->set(UpdateAccountRequest::class, fn (Container $c): UpdateAccountRequest => new UpdateAccountRequest(
    new Validator($c->get(UserManager::class), $c->get(AuthService::class)->getUserId()),
    $c->get(CsrfTokenManager::class),
));

return $container;
