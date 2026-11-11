<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use PDO;

final class UserManager
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }

    public function pseudoExists(string $pseudo): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE pseudo = :pseudo LIMIT 1');
        $stmt->execute(['pseudo' => $pseudo]);

        return $stmt->fetch() !== false;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, pseudo, email, password, avatar, created_at
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->createUserFromRow($row);
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

    private function createUserFromRow(array $row): User
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
