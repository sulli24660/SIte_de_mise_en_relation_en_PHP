<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\DBManager;

class UserManager
{
    public function emailExists(string $email): bool
    {
        $pdo = DBManager::getInstance()->getPDO();

        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }

    public function pseudoExists(string $pseudo): bool
    {
        $pdo = DBManager::getInstance()->getPDO();

        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE pseudo = :pseudo LIMIT 1');
        $stmt->execute(['pseudo' => $pseudo]);

        return $stmt->fetch() !== false;
    }

    public function create(User $user): int
    {
        $pdo = DBManager::getInstance()->getPDO();

        $stmt = $pdo->prepare(
            'INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)'
        );
        $stmt->execute([
            'pseudo' => $user->getPseudo(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);

        return (int) $pdo->lastInsertId();
    }
}
