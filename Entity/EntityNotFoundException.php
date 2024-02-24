<?php

namespace TestTask\Entity;


use RuntimeException;

final class EntityNotFoundException extends RuntimeException
{
    public function __construct(string $entity, int $id)
    {
        parent::__construct(sprintf('Entity %s with id %d not found', $entity, $id), 400);
    }
}
