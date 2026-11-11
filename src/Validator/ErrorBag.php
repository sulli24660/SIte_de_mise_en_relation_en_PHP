<?php

declare(strict_types=1);

namespace App\Validator;

class ErrorBag
{
    private array $errors = [];

    public function add(string $field, string $message): void
    {
        if (!$this->has($field)) {
            $this->errors[$field] = $message;
        }
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function get(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public function isEmpty(): bool
    {
        return $this->errors === [];
    }

    public function all(): array
    {
        return $this->errors;
    }
}
