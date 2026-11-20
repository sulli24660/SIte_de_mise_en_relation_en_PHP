<?php

declare(strict_types=1);

namespace App\Service;

use App\Controller\UserController;
use App\Request\Request;
use Closure;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Conteneur d'injection de dépendances.
 *
 * Chaque service est créé une seule fois par requête HTTP puis réutilisé.
 * Sans fabrique déclarée, le conteneur lit le constructeur de la classe et résout
 * lui-même chaque paramètre typé par une classe (autowiring). Une fabrique (set)
 * reste nécessaire pour ce qui ne se devine pas : scalaires, PDO, variantes d'un même service.
 */
final class Container
{
    /** @var array<class-string, Closure(self): object> */
    private array $factories = [];

    /** @var array<class-string, object> */
    private array $instances = [];

    /** @var array<class-string, true> */
    private array $resolving = [];

    public function __construct()
    {
        $this->instances[self::class] = $this;
    }

    /**
     * @param class-string $id
     * @param Closure(self): object $factory
     */
    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->instances[$id]);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (isset($this->resolving[$id])) {
            throw new LogicException('Dépendance circulaire : ' . implode(' -> ', array_keys($this->resolving)) . ' -> ' . $id);
        }

        $this->resolving[$id] = true;

        try {
            $instance = isset($this->factories[$id])
                ? ($this->factories[$id])($this)
                : $this->autowire($id);
        } finally {
            unset($this->resolving[$id]);
        }

        return $this->instances[$id] = $instance;
    }

    private function autowire(string $class): object
    {
        // $c = App\UserController
        if (!class_exists($class)) {
            throw new LogicException("Service « {$class} » introuvable.");
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new LogicException("« {$class} » n'est pas instanciable : déclarer une fabrique avec set().");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $arguments = array_map(
            fn (ReflectionParameter $parameter): mixed => $this->resolveParameter($class, $parameter),
            $constructor->getParameters(),
        );

        return $reflection->newInstanceArgs($arguments);
    }

    private function resolveParameter(string $class, ReflectionParameter $parameter): mixed
    {
        // Un paramètre optionnel garde sa valeur par défaut : le conteneur n'injecte que l'obligatoire.
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            return $this->get($type->getName());
        }

        throw new LogicException(
            "Impossible de résoudre \${$parameter->getName()} dans {$class}::__construct() : déclarer une fabrique avec set()."
        );
    }
}
