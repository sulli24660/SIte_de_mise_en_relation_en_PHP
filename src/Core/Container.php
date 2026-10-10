<?php

namespace App\Core;

use Closure;
use LogicException;

class Container
{
    /** @var array<string, Closure(self): object> */
    private array $factories = [];

    /** @var array<string, object> */
    private array $instances = [];

    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): object
    {
        if (!isset($this->factories[$id])) {
            throw new LogicException("Aucun service « $id » dans le container.");
        }

        return $this->instances[$id] ??= ($this->factories[$id])($this);
    }
}
