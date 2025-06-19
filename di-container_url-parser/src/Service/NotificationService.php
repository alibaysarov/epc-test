<?php

namespace App\Service;

use App\Logger\Logger;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(private readonly Logger $logger)
    {
    }
    public function process():void
    {
        $this->logger->log("processed!");
    }
}