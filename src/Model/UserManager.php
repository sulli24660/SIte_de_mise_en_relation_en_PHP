<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

final class UserManager extends AbstractManager
{
    protected function getTableName(): string
    {
        return 'users';
    }

    public function create(User $user): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (pseudo, email, password) VALUES (:pseudo, :email, :password)'
        );
        $stmt->execute([
            'pseudo' => $user->getPseudo(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function createEntityFromRow(array $row): User
    {
        $user = new User();
        $user->setId((int) $row['id']);
        $user->setPseudo($row['pseudo']);
        $user->setEmail($row['email']);
        $user->setPassword($row['password']);
        $user->setAvatar($row['avatar']);
        $user->setCreatedAt(new DateTime($row['created_at']));

        return $user;
    }
}