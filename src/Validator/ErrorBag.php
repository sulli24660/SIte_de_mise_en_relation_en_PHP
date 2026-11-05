<?php

declare(strict_types=1);

namespace App\Validator;

/**
 * Regroupe les erreurs de validation d'un formulaire, champ par champ.
 *
 * Une seule erreur est conservée par champ : la première ajoutée.
 * Les erreurs générales (non liées à un champ) utilisent la clé "form".
 */
class ErrorBag
{
    /** @var array<string, string> Messages d'erreur indexés par nom de champ. */
    private array $errors = [];

    /**
     * Ajoute une erreur à un champ, s'il n'en a pas déjà une.
     *
     * @param string $field   Nom du champ concerné (ou "form" pour une erreur générale).
     * @param string $message Message à afficher à l'utilisateur.
     */
    public function add(string $field, string $message): void
    {
        if (!$this->has($field)) {
            $this->errors[$field] = $message;
        }
    }

    /**
     * Indique si un champ a une erreur.
     *
     * @param string $field Nom du champ.
     *
     * @return bool true si une erreur est enregistrée pour ce champ.
     */
    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Retourne le message d'erreur d'un champ.
     *
     * @param string $field Nom du champ.
     *
     * @return string|null Le message, ou null si le champ n'a pas d'erreur.
     */
    public function get(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Indique si aucune erreur n'a été enregistrée.
     *
     * @return bool true si le formulaire est valide.
     */
    public function isEmpty(): bool
    {
        return $this->errors === [];
    }

    /**
     * Retourne toutes les erreurs.
     *
     * @return array<string, string> Messages d'erreur indexés par nom de champ.
     */
    public function all(): array
    {
        return $this->errors;
    }
}
