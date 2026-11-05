<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

/**
 * Fournit la connexion unique à la base de données (patron Singleton).
 *
 * La connexion PDO est créée au premier appel de getInstance(), puis
 * réutilisée pendant toute la requête : on n'ouvre jamais deux
 * connexions. Les paramètres viennent de config/config.php.
 */
class DBManager
{
    /** Instance unique de la classe, créée au premier besoin. */
    private static ?DBManager $instance = null;

    /** Connexion PDO à la base de données. */
    private PDO $pdo;

    /**
     * Ouvre la connexion à la base de données.
     *
     * Constructeur privé : seule getInstance() peut créer l'objet.
     *
     * @throws \PDOException Si la connexion échoue (serveur arrêté, identifiants faux…).
     */
    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                // Toute erreur SQL lance une exception au lieu d'échouer en silence.
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Les résultats sont des tableaux associatifs (clé = nom de colonne).
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Désactive l'émulation des requêtes préparées pour une meilleure sécurité
            ]);
    }

    /**
     * Retourne l'instance unique, en la créant au premier appel.
     *
     * @return DBManager Instance partagée par toute l'application.
     */
    public static function getInstance(): DBManager
    {
        if (self::$instance === null) {
            self::$instance = new DBManager();
        }

        return self::$instance;
    }

    /**
     * @return PDO Connexion à la base de données.
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
