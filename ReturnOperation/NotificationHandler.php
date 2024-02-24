<?php

namespace TestTask\ReturnOperation;


use NW\WebService\References\Operations\Notification\NotificationEvents;
use RuntimeException;
use TestTask\ConfigService;
use TestTask\Entity\Contractor;
use TestTask\Entity\Employee;
use TestTask\Entity\EntityRepository;
use TestTask\Entity\Seller;

final class NotificationHandler implements ReturnHandler
{
    public function __construct(
        private readonly EntityRepository $entityRepository,
        private readonly ConfigService $configService
    ) {
    }

    public function handle(ReturnRequest $data): ReturnResult
    {
        $result = new ReturnResult([
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ]);

        $reseller = $this->entityRepository->getById(Seller::class, $data->getResellerId());
        $client = $this->entityRepository->getById(Contractor::class, $data->getClientId());
        $creator = $this->entityRepository->getById(Employee::class, $data->getCreatorId());
        $expert = $this->entityRepository->getById(Employee::class, $data->getExpertId());

        $templateData = [
            'COMPLAINT_ID' => $data->getComplaintId(),
            'COMPLAINT_NUMBER' => $data->getComplaintNumber(),
            'CREATOR_ID' => $data->getCreatorId(),
            'CREATOR_NAME' => $creator->getFullName(),
            'EXPERT_ID' => $data->getExpertId(),
            'EXPERT_NAME' => $expert->getName(),
            'CLIENT_ID' => $client->getId(),
            'CLIENT_NAME' => $client->getFullName(),
            'CONSUMPTION_ID' => $data->getConsumptionId(),
            'CONSUMPTION_NUMBER' => $data->getConsumptionNumber(),
            'AGREEMENT_NUMBER' => $data->getAgreementNumber(),
            'DATE' => $data->getDate()->format('Y-m-d H:i:s'),
            'DIFFERENCES' => $this->getDifferences($data, $reseller),
        ];

        foreach ($templateData as $key => $value) {
            if (empty($value)) {
                throw new RuntimeException("Template Data ({$key}) is empty!", 500);
            }
        }

        $this->sendNotificationToEmployees($templateData, $reseller, $result);
        $this->sendNotificationToClient($data, $templateData, $reseller, $client, $result);

        return $result;
    }

    private function sendNotificationToEmployees(array $templateData, Seller $reseller, ReturnResult $result): void
    {
        $employeeEmails = $this->configService->getEmailsByPermit($reseller->getId(), 'tsGoodsReturn');
        if (empty($reseller->getEmail()) || count($employeeEmails) <= 0) {
            return;
        }
        foreach ($employeeEmails as $email) {
            MessagesClient::sendMessage([
                0 => [ // MessageTypes::EMAIL
                       'emailFrom' => $reseller->getEmail(),
                       'emailTo' => $email,
                       'subject' => __('complaintEmployeeEmailSubject', $templateData, $reseller->getId()),
                       'message' => __('complaintEmployeeEmailBody', $templateData, $reseller->getId()),
                ],
            ], $reseller->getId(), NotificationEvents::CHANGE_RETURN_STATUS);
        }
        $result->addValue('notificationEmployeeByEmail', true);
    }

    private function sendNotificationToClient(
        ReturnRequest $data,
        array $templateData,
        Seller $reseller,
        Contractor $client,
        ReturnResult $result
    ): void {
        if ($data->getNotificationType() !== ReturnRequest::TYPE_CHANGE
            || empty($data->getDifferences()['to'])
        ) {
            return;
        }

        if (!empty($reseller->getEmail()) && !empty($client->getEmail())) {
            MessagesClient::sendMessage([
                [ // MessageTypes::EMAIL
                  'emailFrom' => $reseller->getEmail(),
                  'emailTo' => $client->getEmail(),
                  'subject' => __('complaintClientEmailSubject', $templateData, $reseller->getId()),
                  'message' => __('complaintClientEmailBody', $templateData, $reseller->getId()),
                ]
            ],
                $reseller->getId(),
                $client->getId(),
                NotificationEvents::CHANGE_RETURN_STATUS,
                (int)$data->getDifferences()['to']
            );
            $result->addValue('notificationClientByEmail', true);
        }

        if (!empty($client->getMobile())) {
            $response = NotificationManager::send(
                $reseller->getId(),
                $client->getId(),
                NotificationEvents::CHANGE_RETURN_STATUS,
                (int)$data->getDifferences()['to'],
                $templateData
            );
            if ($response) {
                $smsResult = ['isSent' => true];
                if (isset($response['error'])) {
                    $smsResult['message'] = $response['error'];
                }
                $result->addValue('notificationClientBySms', $smsResult);
            }
        }
    }

    private function getDifferences(ReturnRequest $data, Seller $reseller): string
    {
        $differences = '';
        if ($data->getNotificationType() === ReturnRequest::TYPE_NEW) {
            $differences = __('NewPositionAdded', null, $reseller->getId());
        } elseif ($data->getNotificationType() === ReturnRequest::TYPE_CHANGE && !empty($data['differences'])) {
            $differences = __('PositionStatusHasChanged', [
                'FROM' => (new Status((int)$data['differences']['from']))->getName(),
                'TO' => (new Status((int)$data['differences']['to']))->getName(),
            ], $reseller->getId());
        }
        return $differences;
    }
}
