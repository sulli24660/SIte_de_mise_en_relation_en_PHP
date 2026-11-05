<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\AuthService;

/**
 * Réserve l'accès d'une page aux utilisateurs connectés.
 *
 * Appelé par le Router avant le contrôleur, pour chaque action
 * déclarée comme protégée. Un visiteur non connecté est redirigé
 * vers le formulaire de connexion, et le contrôleur n'est jamais exécuté.
 */
final class AuthMiddleware
{
    /**
     * @param AuthService $authService Service qui indique si un utilisateur est connecté.
     */
    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * Laisse passer la requête si l'utilisateur est connecté,
     * sinon le redirige vers la page de connexion.
     *
     * La méthode ne retourne rien : si elle se termine normalement,
     * le Router poursuit et appelle le contrôleur.
     */
    public function handle(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: index.php?action=login');
            // exit est indispensable : sans lui, le Router continuerait
            // et exécuterait quand même le contrôleur de la page privée.
            exit;
        }
    }
}
