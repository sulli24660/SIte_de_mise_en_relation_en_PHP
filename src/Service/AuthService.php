<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Centralise la gestion de l'utilisateur connecté.
 *
 * C'est la seule classe qui lit ou écrit l'identifiant de l'utilisateur
 * en session : le reste de l'application passe par ses méthodes
 * (isLoggedIn, getUserId, login, logout) et ne manipule jamais
 * $_SESSION directement pour l'authentification.
 *
 * Prérequis : la session doit être démarrée (voir public/index.php).
 */
final class AuthService
{
    /** Clé sous laquelle l'identifiant de l'utilisateur est rangé dans $_SESSION. */
    private const SESSION_KEY = 'user_id';

    /**
     * Indique si un utilisateur est connecté.
     */
    public function isLoggedIn(): bool
    {
        return $this->getUserId() !== null;
    }

    /**
     * Retourne l'identifiant de l'utilisateur connecté.
     *
     * @return int|null L'identifiant, ou null si personne n'est connecté.
     */
    public function getUserId(): ?int
    {
        $userId = $_SESSION[self::SESSION_KEY] ?? null;

        // On ne fait confiance qu'à un entier : toute autre valeur
        // (session corrompue, type inattendu) équivaut à "non connecté".
        return is_int($userId) ? $userId : null;
    }

    /**
     * Connecte l'utilisateur en enregistrant son identifiant en session.
     *
     * À appeler uniquement après avoir vérifié ses identifiants.
     */
    public function login(int $userId): void
    {
        // Nouvel identifiant de session : un identifiant obtenu avant la
        // connexion (fixation de session) devient inutilisable.
        session_regenerate_id(true);

        $_SESSION[self::SESSION_KEY] = $userId;
    }

    /**
     * Déconnecte l'utilisateur : vide et détruit la session, supprime son cookie.
     */
    public function logout(): void
    {
        // 1. Vider les données de la session.
        $_SESSION = [];

        // 2. Supprimer le cookie de session dans le navigateur, avec une date
        //    d'expiration passée et les mêmes paramètres qu'à sa création.
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);

        // 3. Détruire la session côté serveur.
        session_destroy();
    }
}