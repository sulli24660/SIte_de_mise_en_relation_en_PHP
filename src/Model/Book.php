<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

/**
 * Entité représentant un livre proposé à l'échange (table "books").
 *
 * Simple conteneur de données : les setters enregistrent les valeurs
 * telles quelles, sans les transformer ni les valider.
 */
class Book
{
    /** Identifiant unique du livre. */
    private int $id;

    /** Identifiant de l'utilisateur propriétaire du livre. */
    private int $userId;

    /** Titre du livre. */
    private string $title;

    /** Auteur du livre. */
    private string $author;

    /** Nom du fichier image de la couverture, ou null s'il n'y en a pas. */
    private ?string $image = null;

    /** Description du livre par son propriétaire, ou null. */
    private ?string $description = null;

    /** true si le livre est disponible à l'échange. */
    private bool $isAvailable = true;

    /** Date d'ajout du livre. */
    private DateTime $createdAt;

    /**
     * @return int Identifiant unique du livre.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id Identifiant unique du livre.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int Identifiant du propriétaire du livre.
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId Identifiant du propriétaire du livre.
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string Titre du livre.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title Titre du livre.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string Auteur du livre.
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author Auteur du livre.
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string|null Nom du fichier image de la couverture, ou null.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image Nom du fichier image de la couverture, ou null.
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string|null Description du livre, ou null.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description Description du livre, ou null.
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return bool true si le livre est disponible à l'échange.
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * @param bool $isAvailable true si le livre est disponible à l'échange.
     */
    public function setIsAvailable(bool $isAvailable): void
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return DateTime Date d'ajout du livre.
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt Date d'ajout du livre.
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
