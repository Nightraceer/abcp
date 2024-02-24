<?php

use TestTask\ConfigService;
use TestTask\Entity\TestEntityRepository;
use TestTask\ReturnOperation\NotificationHandler;
use TestTask\ReturnOperation\ReturnRequest;


$entityRepository = new TestEntityRepository();
$returnHandlers = [
    new NotificationHandler($entityRepository, new ConfigService())
];


$returnRequest = ReturnRequest::fromArray($_REQUEST['data'] ?? []);

foreach ($returnHandlers as $returnHandler) {
    $result = $returnHandler->handle($returnRequest);
}
