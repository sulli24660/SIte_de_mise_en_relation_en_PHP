<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use PDO;

/**
 * Accès aux données de la table "users".
 *
 * Ce manager ne contient que des requêtes SQL : la validation des
 * données et le hachage du mot de passe sont faits en amont, dans
 * le contrôleur.
 */
final class UserManager
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Indique si un compte utilise déjà cette adresse e-mail.
     */
    public function emailExists(string $email): bool
    {
        // SELECT 1 : on teste seulement l'existence, sans lire de données.
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }

    /**
     * Indique si un compte utilise déjà ce pseudo.
     */
    public function pseudoExists(string $pseudo): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE pseudo = :pseudo LIMIT 1');
        $stmt->execute(['pseudo' => $pseudo]);

        return $stmt->fetch() !== false;
    }

    /**
     * Retrouve un utilisateur à partir de son adresse e-mail.
     *
     * Utilisé à la connexion : l'objet retourné contient le mot de
     * passe haché, qui ne doit servir qu'à password_verify() et ne
     * doit jamais être transmis à une vue.
     *
     * @return User|null L'utilisateur, ou null si aucun compte ne correspond.
     */
    public function findByEmail(string $email): ?User
    {
        // Colonnes listées explicitement plutôt que SELECT * : on ne lit
        // que ce dont on a besoin, même si la table évolue plus tard.
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

    /**
     * Enregistre un nouvel utilisateur.
     *
     * @param User $user Utilisateur dont le mot de passe est déjà haché.
     *
     * @return int Identifiant attribué par la base.
     */
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

    /**
     * Transforme une ligne de la table "users" en objet User.
     *
     * @param array<string, mixed> $row Ligne retournée par PDO.
     */
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