<?php

declare(strict_types=1);

namespace App\Validator;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Erreurs de validation par champ, disponibles dans toutes les vues via $errors.
 *
 * @implements IteratorAggregate<string, string>
 */
final class ErrorBag implements Countable, IteratorAggregate
{
    /** @param array<string, string> $errors */
    public function __construct(private readonly array $errors = [])
    {
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function first(string $field): string
    {
        return $this->errors[$field] ?? '';
    }

    public function any(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->errors;
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->errors);
    }
}
