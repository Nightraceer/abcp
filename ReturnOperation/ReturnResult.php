<?php

namespace TestTask\ReturnOperation;

final class ReturnResult
{
    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addValue(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }
}
