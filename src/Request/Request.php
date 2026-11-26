<?php

declare(strict_types=1);

namespace App\Request;

class Request
{
    private readonly array $query;
    private readonly array $body;
    private readonly string $method;
    private readonly array $files;

    public function __construct(?array $query = null, ?array $body = null, ?string $method = null, ?array $files = null)
    {
        $this->query = $query ?? $_GET;
        $this->body = $body ?? $_POST;
        $this->method = strtoupper($method ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->files = $files ?? $_FILES;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function query(string $key, string $default = ''): string
    {
        return trim($this->findString($this->query, $key) ?? $default);
    }

    public function queryInt(string $key, int $default = 0): int
    {
        return $this->toInt($this->findString($this->query, $key), $default);
    }

    public function queryString(): string
    {
        return http_build_query($this->query);
    }

    public function post(string $key, string $default = ''): string
    {
        return trim($this->findString($this->body, $key) ?? $default);
    }

    // Sans trim : dans un mot de passe, chaque caractère compte.
    public function postRaw(string $key): string
    {
        return $this->findString($this->body, $key) ?? '';
    }

    public function postInt(string $key, int $default = 0): int
    {
        return $this->toInt($this->findString($this->body, $key), $default);
    }

    // Renvoie null si aucun fichier n'a été choisi, ou si le champ a été trafiqué (plusieurs fichiers, structure inattendue).
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;

        if (!is_array($file) || !is_string($file['tmp_name'] ?? null) || !is_int($file['error'] ?? null)) {
            return null;
        }

        return $file['error'] === UPLOAD_ERR_NO_FILE ? null : $file;
    }

    private function findString(array $source, string $key): ?string
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