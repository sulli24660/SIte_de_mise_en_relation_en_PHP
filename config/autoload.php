<?php

use App\Exceptions\NamespaceInvalidException;

const ALIASES = [
    'App' => 'src'
];

spl_autoload_register(function (string $class): void {
    $namespaceParts = explode('\\', $class);




    if (!in_array($namespaceParts[0], array_keys(ALIASES))) {
        throw new NamespaceInvalidException($namespaceParts[0]);
    }
    $namespaceParts[0] = ALIASES[$namespaceParts[0]];

    $filepath = dirname(__DIR__) . '/' . implode('/', $namespaceParts) . '.php';

    if (!file_exists($filepath)) {
        // envoie de sms
        throw new Exception("Fichier « " . $filepath . " » introuvable pour la classe « " . $class . " ». Vérifier le chemin, le nom de la classe ou le namespace");
    }

    require $filepath;
});
