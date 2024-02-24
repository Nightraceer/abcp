<?php

namespace TestTask;


final class ConfigService
{
    public function getEmailsByPermit(int $resellerId, string $event): array
    {
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}
