<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Gère le jeton anti-CSRF (Cross-Site Request Forgery).
 *
 * Un jeton secret et aléatoire est stocké en session puis inséré
 * dans chaque formulaire. À la soumission, le jeton reçu doit être
 * identique à celui de la session : un site tiers ne pouvant pas
 * le connaître, ses requêtes forgées sont rejetées.
 *
 * Prérequis : la session doit être démarrée (voir public/index.php).
 */
final class CsrfTokenManager
{
    /** Clé sous laquelle le jeton est rangé dans $_SESSION. */
    private const SESSION_KEY = 'csrf_token';

    /**
     * Retourne le jeton de la session, en le créant au premier appel.
     *
     * @return string Jeton de 64 caractères hexadécimaux.
     */
    public function getToken(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            // random_bytes() est un générateur cryptographiquement sûr :
            // le résultat est imprévisible, contrairement à rand() ou uniqid().
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Vérifie que le jeton reçu correspond à celui de la session.
     *
     * @param mixed $token Valeur reçue du formulaire (peut être absente ou d'un type inattendu).
     *
     * @return bool true si le jeton est valide, false sinon.
     */
    public function isValid(mixed $token): bool
    {
        $expected = $_SESSION[self::SESSION_KEY] ?? null;

        // hash_equals() compare en temps constant : la durée de comparaison
        // ne révèle pas combien de caractères sont corrects (attaque temporelle).
        return is_string($token)
            && is_string($expected)
            && hash_equals($expected, $token);
    }
}