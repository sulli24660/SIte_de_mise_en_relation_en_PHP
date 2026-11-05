<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
use App\Middleware\AuthMiddleware;
use App\Model\BookManager;
use App\Model\UserManager;

/**
 * Aiguille chaque requête vers le bon contrôleur.
 *
 * Le Router est aussi le point d'assemblage de l'application :
 * c'est le seul endroit qui crée les contrôleurs et leurs
 * dépendances (managers, vue, services), puis les leur injecte.
 */
final class Router
{
    /**
     * Actions réservées aux utilisateurs connectés.
     *
     * Toute nouvelle page privée doit être ajoutée ici : le contrôle
     * d'accès est ainsi centralisé et ne peut pas être oublié.
     */
    private const PROTECTED_ACTIONS = ['account', 'messages'];

    /**
     * Exécute l'action demandée.
     *
     * @param string               $action Nom de l'action (paramètre "action" de l'URL).
     * @param array<string, mixed> $params Paramètres de la requête (en général $_GET).
     *
     * @throws NotFoundException Si l'action n'existe pas.
     */
    public function dispatch(string $action, array $params = []): void
    {
        // Contrôle d'accès avant toute exécution de contrôleur.
        // true : comparaison stricte (même type et même valeur).
        if (in_array($action, self::PROTECTED_ACTIONS, true)) {
            (new AuthMiddleware(new AuthService()))->handle();
        }

        switch ($action) {
            case 'books':
                $this->createBookController()->index();
                break;

            case 'book':
                // Cast en entier : un id non numérique devient 0 et mène à une page 404.
                $this->createBookController()->show((int) ($params['id'] ?? 0));
                break;

            case 'register':
                $this->createUserController()->register();
                break;

            case 'login':
                $this->createUserController()->login();
                break;

            case 'logout':
                $this->createUserController()->logout();
                break;

            default:
                throw new NotFoundException();
        }
    }

    /**
     * Construit le contrôleur des livres avec ses dépendances.
     *
     * @return BookController Contrôleur prêt à l'emploi.
     */
    private function createBookController(): BookController
    {
        $pdo = DBManager::getInstance()->getPDO();

        return new BookController(
            new BookManager($pdo),
            new View(new CsrfTokenManager(), new AuthService()),
        );
    }

    /**
     * Construit le contrôleur des utilisateurs avec ses dépendances.
     *
     * Les services sont créés une seule fois et partagés entre la vue
     * et le contrôleur.
     *
     * @return UserController Contrôleur prêt à l'emploi.
     */
    private function createUserController(): UserController
    {
        $pdo = DBManager::getInstance()->getPDO();
        $csrfTokenManager = new CsrfTokenManager();
        $authService = new AuthService();

        return new UserController(
            new UserManager($pdo),
            new View($csrfTokenManager, $authService),
            $csrfTokenManager,
            $authService,
        );
    }
}
