<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Réserve l'accès d'une page aux utilisateurs connectés.
 *
 * Appelé par le Router avant le contrôleur
 */
final class AuthMiddleware
{
    /**
     * Laisse passer la requête si l'utilisateur est connecté,
     * sinon le redirige vers la page de connexion.
     */
    public function handle(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }
}