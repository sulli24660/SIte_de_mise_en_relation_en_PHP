<?php

declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;
use PDO;

abstract class AbstractManager
{
    public function __construct(protected readonly PDO $pdo)
    {
    }

    abstract protected function getTableName(): string;

    /**
     * @return list<string>
     */
    abstract protected function getSearchableColumns(): array;

    abstract protected function createEntityFromRow(array $row): object;

    protected function getDefaultOrderBy(): string
    {
        return 'id ASC';
    }

    public function findAll(): array
    {
        $sql = sprintf('SELECT * FROM %s ORDER BY %s', $this->getTableName(), $this->getDefaultOrderBy());

        return array_map(
            fn (array $row): object => $this->createEntityFromRow($row),
            $this->pdo->query($sql)->fetchAll()
        );
    }

    public function findById(int $id): ?object
    {
        return $this->findOneBy('id', $id);
    }

    public function findOneBy(string $column, string|int $value): ?object
    {
        $this->assertColumnIsSearchable($column);

        $stmt = $this->pdo->prepare(
            sprintf('SELECT * FROM %s WHERE %s = :value LIMIT 1', $this->getTableName(), $column)
        );
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();

        return $row === false ? null : $this->createEntityFromRow($row);
    }

    public function exists(string $column, string|int $value): bool
    {
        $this->assertColumnIsSearchable($column);

        $stmt = $this->pdo->prepare(
            sprintf('SELECT 1 FROM %s WHERE %s = :value LIMIT 1', $this->getTableName(), $column)
        );
        $stmt->execute(['value' => $value]);

        return $stmt->fetch() !== false;
    }

    // Un nom de colonne ne peut pas passer par prepare() : seule une liste blanche empêche l'injection SQL.
    private function assertColumnIsSearchable(string $column): void
    {
        if (!in_array($column, $this->getSearchableColumns(), true)) {
            throw new InvalidArgumentException("Colonne non autorisée pour la recherche : « $column ».");
        }
    }
}