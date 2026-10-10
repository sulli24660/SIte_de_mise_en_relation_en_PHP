<?php

use App\Controller\BookController;
use App\Core\Container;
use App\Core\Router;
use App\Core\RouterInterface;
use App\Core\SuperRouter;

$container = new Container();

$container->set(RouterInterface::class, function (): RouterInterface {
    $router = new SuperRouter();
//    $router = new Router();

    return $router;
});

return $container;
