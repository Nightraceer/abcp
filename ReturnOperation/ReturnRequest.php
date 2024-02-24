<?php

namespace TestTask\ReturnOperation;


use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

final class ReturnRequest
{
    public const TYPE_NEW = 1;
    public const TYPE_CHANGE = 2;

    /**
     * @param array{from: int, to: int} $differences
     */
    public function __construct(
        private readonly int $resellerId,
        private readonly int $notificationType,
        private readonly int $clientId,
        private readonly int $creatorId,
        private readonly int $expertId,
        private readonly int $complaintId,
        private readonly string $complaintNumber,
        private readonly int $consumptionId,
        private readonly string $consumptionNumber,
        private readonly string $agreementNumber,
        private readonly DateTimeInterface $date,
        private readonly array $differences = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $requiredFields = [
            'resellerId',
            'notificationType',
            'clientId',
            'creatorId',
            'expertId',
            'complaintId',
            'complaintNumber',
            'consumptionId',
            'consumptionNumber',
            'agreementNumber',
            'date',
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException(sprintf('Field %s is required', $field));
            }
        }

        return new self(
            $data['resellerId'] ?? '',
            $data['notificationType'] ?? '',
            $data['clientId'] ?? '',
            $data['creatorId'] ?? '',
            $data['expertId'] ?? '',
            $data['complaintId'] ?? '',
            $data['complaintNumber'] ?? '',
            $data['consumptionId'] ?? '',
            $data['consumptionNumber'] ?? '',
            $data['agreementNumber'] ?? '',
            new DateTimeImmutable($data['date'] ?? 'now'),
            $data['differences'] ?? [],
        );
    }

    public function getResellerId(): int
    {
        return $this->resellerId;
    }

    public function getNotificationType(): int
    {
        return $this->notificationType;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getExpertId(): int
    {
        return $this->expertId;
    }

    public function getComplaintId(): int
    {
        return $this->complaintId;
    }

    public function getComplaintNumber(): string
    {
        return $this->complaintNumber;
    }

    public function getConsumptionId(): int
    {
        return $this->consumptionId;
    }

    public function getConsumptionNumber(): string
    {
        return $this->consumptionNumber;
    }

    public function getAgreementNumber(): string
    {
        return $this->agreementNumber;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return array{from: int, to: int}
     */
    public function getDifferences(): array
    {
        return $this->differences;
    }
}
