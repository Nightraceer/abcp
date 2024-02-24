<?php

namespace TestTask\Entity;

interface EntityRepository
{
    /**
     * @template T of object
     * @param class-string<T> $entityClass
     * @param int $id
     *
     * @return T
     * @throws EntityNotFoundException
     */
    public function getById(string $entityClass, int $id): object;
}
