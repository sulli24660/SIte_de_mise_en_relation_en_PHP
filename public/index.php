<?php

/**
 * Point d'entrée unique de l'application TomTroc.
 *
 * Toutes les requêtes passent par ce fichier (front controller) :
 * il charge la configuration, démarre la session, puis confie
 * l'action demandée au Router. Les erreurs non gérées sont
 * interceptées ici pour afficher une page d'erreur propre.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/config/autoload.php';

use App\Exception\NotFoundException;
use App\Service\Router;
use App\Service\View;

// Session démarrée une seule fois, avant tout affichage, pour toutes les pages.
session_start([
    // Le cookie de session est inaccessible au JavaScript (limite le vol par XSS).
    'cookie_httponly' => true,
    // Le cookie n'est pas envoyé lors d'un formulaire soumis depuis un autre site (CSRF).
    'cookie_samesite' => 'Lax',
    // Cookie transmis uniquement en HTTPS quand le site l'utilise (production).
    // En local (php -S, HTTP), il reste désactivé pour que la session fonctionne.
    'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    // Refuse un identifiant de session non créé par le serveur (fixation de session).
    'use_strict_mode' => true,
]);

// On vérifie que l'action est bien une chaîne : "?action[]=x" enverrait un tableau.
$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'books';

try {
    (new Router())->dispatch($action, $_GET);
} catch (NotFoundException $e) {
    // Page ou ressource inexistante : code HTTP 404 et message prévu pour l'utilisateur.
    http_response_code($e->getCode());
    (new View())->render('error', ['message' => $e->getMessage()]);
} catch (Throwable $e) {
    // Erreur inattendue : le détail technique va dans le journal du serveur,
    // jamais à l'écran, pour ne pas révéler d'informations sur l'application.
    error_log((string) $e);
    http_response_code(500);
    (new View())->render('error', ['message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
}