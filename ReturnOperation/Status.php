<?php

namespace TestTask\ReturnOperation;


use UnexpectedValueException;

final class Status
{
    private const STATUSES = [
        0 => 'Completed',
        1 => 'Pending',
        2 => 'Rejected',
    ];

    public function __construct(private readonly int $id)
    {
        if (!array_key_exists($id, self::STATUSES)) {
            throw new UnexpectedValueException('Invalid status id');
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return self::STATUSES[$this->id];
    }

    public function withId(int $id): self
    {
        return new self($id);
    }
}
