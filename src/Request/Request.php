<?php

declare(strict_types=1);

namespace App\Request;

/**
 * Point d'entrée unique des données utilisateur.
 * Normalise (string uniquement, trim, cast) mais n'échappe pas le HTML :
 * l'échappement se fait à l'affichage, avec htmlspecialchars() dans les vues.
 * Sans argument, lit les superglobales ; les tests passent leurs propres tableaux.
 */
class Request
{
    private readonly array $query;
    private readonly array $body;
    private readonly string $method;

    public function __construct(?array $query = null, ?array $body = null, ?string $method = null)
    {
        $this->query = $query ?? $_GET;
        $this->body = $body ?? $_POST;
        $this->method = strtoupper($method ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function query(string $key, string $default = ''): string
    {
        return trim($this->raw($this->query, $key) ?? $default);
    }

    public function queryInt(string $key, int $default = 0): int
    {
        return $this->toInt($this->raw($this->query, $key), $default);
    }

    public function post(string $key, string $default = ''): string
    {
        return trim($this->raw($this->body, $key) ?? $default);
    }

    /** Sans trim : pour les mots de passe, où chaque caractère compte. */
    public function postRaw(string $key): string
    {
        return $this->raw($this->body, $key) ?? '';
    }

    public function postInt(string $key, int $default = 0): int
    {
        return $this->toInt($this->raw($this->body, $key), $default);
    }

    /** Rejette les tableaux (ex. ?id[]=1) : seule une string est acceptée. */
    private function raw(array $source, string $key): ?string
    {
        $value = $source[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    private function toInt(?string $value, int $default): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);

        return $int === false ? $default : $int;
    }
}
