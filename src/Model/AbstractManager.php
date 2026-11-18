<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

abstract class AbstractManager
{
    public function __construct(protected readonly PDO $pdo)
    {
    }

    abstract protected function getTableName(): string;

    abstract protected function createEntityFromRow(array $row): object;

    protected function getDefaultOrderBy(): string
    {
        return 'id ASC';
    }

    public function findAll(): array
    {
        $sql = sprintf('SELECT * FROM %s ORDER BY %s', $this->getTableName(), $this->getDefaultOrderBy());

        return $this->createEntitiesFromRows($this->pdo->query($sql)->fetchAll());
    }

    public function findById(int $id): ?object
    {
        return $this->findOneBy('id', $id);
    }

    // findOneBy(), findAllBy() et exists() insèrent $column tel quel dans le SQL : il doit toujours être écrit en dur, jamais venir de l'utilisateur.
    public function findOneBy(string $column, string|int $value): ?object
    {
        $stmt = $this->pdo->prepare(
            sprintf('SELECT * FROM %s WHERE %s = :value LIMIT 1', $this->getTableName(), $column)
        );
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();

        return $row === false ? null : $this->createEntityFromRow($row);
    }

    public function findAllBy(string $column, string|int $value): array
    {
        $stmt = $this->pdo->prepare(
            sprintf(
                'SELECT * FROM %s WHERE %s = :value ORDER BY %s',
                $this->getTableName(),
                $column,
                $this->getDefaultOrderBy()
            )
        );
        $stmt->execute(['value' => $value]);

        return $this->createEntitiesFromRows($stmt->fetchAll());
    }

    public function exists(string $column, string|int $value, ?int $exceptId = null): bool
    {
        $sql = sprintf('SELECT 1 FROM %s WHERE %s = :value', $this->getTableName(), $column);
        $params = ['value' => $value];

        if ($exceptId !== null) {
            $sql .= ' AND id != :exceptId';
            $params['exceptId'] = $exceptId;
        }

        $stmt = $this->pdo->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return $stmt->fetch() !== false;
    }

    private function createEntitiesFromRows(array $rows): array
    {
        return array_map(fn (array $row): object => $this->createEntityFromRow($row), $rows);
    }
}
