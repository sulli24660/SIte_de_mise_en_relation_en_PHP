<?php

/**
 * Autoloader maison de l'application (sans Composer).
 *
 * Quand PHP rencontre une classe pas encore chargée, il appelle la
 * fonction enregistrée ci-dessous, qui transforme le nom complet de la
 * classe en chemin de fichier, puis inclut ce fichier.
 * Exemple : App\Model\Book → src/Model/Book.php
 */

declare(strict_types=1);

/** Correspondance entre le premier segment du namespace et le dossier racine. */
const ALIASES = [
    'App' => 'src'
];

/**
 * @param string $class Nom complet de la classe (ex. "App\Model\Book").
 *
 * @throws Exception Si le namespace ne commence pas par "App" ou si le fichier est introuvable.
 */
spl_autoload_register(function (string $class): void {
    $namespaceParts = explode('\\', $class);

    if (in_array($namespaceParts[0], array_keys(ALIASES))) {
        $namespaceParts[0] = ALIASES[$namespaceParts[0]];
    } else {
        throw new Exception('Namespace « ' . $namespaceParts[0] . ' » invalide. Un namespace doit commencer par « App »');
    }
    $filepath = dirname(__DIR__) . '/' . implode('/', $namespaceParts) . '.php';
    if (!file_exists($filepath)) {
        throw new Exception("Fichier « " . $filepath . " » introuvable pour la classe « " . $class . " ». Vérifier le chemin, le nom de la classe ou le namespace");
    }
    require $filepath;
});
