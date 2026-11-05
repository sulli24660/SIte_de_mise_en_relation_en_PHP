<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

/**
 * Entité représentant un membre de TomTroc (table "users").
 *
 * Simple conteneur de données : les setters enregistrent les valeurs
 * telles quelles, sans les transformer ni les valider. En particulier,
 * le mot de passe doit être haché AVANT d'être passé à setPassword().
 */
class User
{
    /** Identifiant unique de l'utilisateur. */
    private int $id;

    /** Pseudo public de l'utilisateur. */
    private string $pseudo;

    /** Adresse e-mail, utilisée pour la connexion. */
    private string $email;

    /** Mot de passe haché (jamais en clair). */
    private string $password;

    /** Nom du fichier image de l'avatar, ou null s'il n'y en a pas. */
    private ?string $avatar = null;

    /** Date d'inscription. */
    private DateTime $createdAt;

    /**
     * @return int Identifiant unique de l'utilisateur.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id Identifiant unique de l'utilisateur.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string Pseudo de l'utilisateur.
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo Pseudo de l'utilisateur.
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * @return string Adresse e-mail de l'utilisateur.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email Adresse e-mail de l'utilisateur.
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retourne le mot de passe haché.
     *
     * À n'utiliser que pour password_verify() ou l'enregistrement en base :
     * cette valeur ne doit jamais être transmise à une vue.
     *
     * @return string Mot de passe haché.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password Mot de passe déjà haché avec password_hash().
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null Nom du fichier image de l'avatar, ou null.
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar Nom du fichier image de l'avatar, ou null.
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @return DateTime Date d'inscription.
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt Date d'inscription.
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
