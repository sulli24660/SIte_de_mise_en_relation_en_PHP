<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Affiche une vue à l'intérieur du layout commun (header, footer).
 *
 * En plus des données fournies par le contrôleur, chaque vue reçoit
 * automatiquement les données communes à toutes les pages :
 * - $isLoggedIn : true si un utilisateur est connecté ;
 * - $csrfToken  : jeton anti-CSRF, pour les formulaires (ex. déconnexion).
 */
final class View
{
    /**
     * @param CsrfTokenManager $csrfTokenManager Fournit le jeton anti-CSRF des formulaires.
     * @param AuthService      $authService      Indique si un utilisateur est connecté.
     */
    public function __construct(
        private readonly CsrfTokenManager $csrfTokenManager,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * Affiche une vue dans le layout.
     *
     * @param string               $view Chemin de la vue, sans extension, depuis le dossier views (ex. "books/index").
     * @param array<string, mixed> $data Variables à rendre disponibles dans la vue.
     */
    public function render(string $view, array $data = []): void
    {
        // Les données communes sont fixées ici et écrasent toute valeur du même
        // nom venant du contrôleur : elles restent donc toujours fiables.
        $data['isLoggedIn'] = $this->authService->isLoggedIn();
        $data['csrfToken'] = $this->csrfTokenManager->getToken();

        // EXTR_SKIP : une clé de $data ne peut pas écraser une variable déjà définie.
        extract($data, EXTR_SKIP);

        $templatePath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        require dirname(__DIR__, 2) . '/views/layout.php';
    }
}
