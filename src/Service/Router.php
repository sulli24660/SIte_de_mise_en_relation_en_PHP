<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\BookController;
use App\Controller\UserController;
use App\Exception\NotFoundException;
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
     * Exécute l'action demandée.
     *
     * @param string $action Nom de l'action (paramètre "action" de l'URL).
     * @param array  $params Paramètres de la requête (en général $_GET).
     *
     * @throws NotFoundException Si l'action n'existe pas.
     */
    public function dispatch(string $action, array $params = []): void
    {
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
     */
    private function createBookController(): BookController
    {
        $pdo = DBManager::getInstance()->getPDO();

        return new BookController(
            new BookManager($pdo),
            new View(new CsrfTokenManager()),
        );
    }

    /**
     * Construit le contrôleur des utilisateurs avec ses dépendances.
     */
    private function createUserController(): UserController
    {
        $pdo = DBManager::getInstance()->getPDO();
        $csrfTokenManager = new CsrfTokenManager();

        return new UserController(
            new UserManager($pdo),
            new View($csrfTokenManager),
            $csrfTokenManager,
        );
    }
}