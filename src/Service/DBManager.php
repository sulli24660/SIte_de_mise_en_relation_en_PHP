<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

class DBManager
{
    private static ?DBManager $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Désactive l'émulation des requêtes préparées pour une meilleure sécurité
            ]);
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
