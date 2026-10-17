<?php

declare(strict_types=1);

namespace App\Service;

use PDO;
use PDOException;

class DBManager
{
    private static ?DBManager $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Désactive l'émulation des requêtes préparées pour une meilleure sécurité
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données ' . $e->getMessage());
        }
    }

    public static function getInstance(): DBManager
    {
        if (self::$instance === null) {
            self::$instance = new DBManager();
        }

        return self::$instance;
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
