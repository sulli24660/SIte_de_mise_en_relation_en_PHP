<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\DBManager;

class UserManager
{
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
