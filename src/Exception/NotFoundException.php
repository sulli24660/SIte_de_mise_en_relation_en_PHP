<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

/**
 * Signale qu'une page ou une ressource demandée n'existe pas.
 *
 * Interceptée dans public/index.php, elle produit une réponse HTTP 404
 * et affiche son message dans la page d'erreur.
 */
class NotFoundException extends \Exception
{
    /**
     * @param string $message Message affiché à l'utilisateur sur la page d'erreur.
     */
    public function __construct(string $message = 'Page Introuvable')
    {
        // Le code 404 est réutilisé par index.php comme code de réponse HTTP.
        parent::__construct($message, 404);
    }
}
