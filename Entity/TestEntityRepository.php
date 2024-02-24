<?php

namespace TestTask\Entity;

final class TestEntityRepository implements EntityRepository
{
    private array $entities = [
        Contractor::class => [1, 2, 3],
        Employee::class => [4, 5, 6],
        Seller::class => [7, 8, 9],
    ];

    public function getById(string $entityClass, int $id): object
    {
        if (!array_key_exists($entityClass, $this->entities)
            || !in_array($id, $this->entities[$entityClass], true)
        ) {
            throw new EntityNotFoundException($entityClass, $id);
        }
        return new $entityClass($id);
    }
}
