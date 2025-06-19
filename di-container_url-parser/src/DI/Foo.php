<?php

namespace App\DI;

use App\Database\DB;
use App\Logger\Logger;
use App\Service\NotificationService;
use App\Service\NotificationServiceInterface;

class Foo
{
    public function __construct(private readonly Logger $logger,private readonly NotificationServiceInterface $notificationService)
    {
    }

    function processHandler():void
    {
        $this->notificationService->process();
    }
    function sayHi():void
    {
        $this->logger->log("Hello World");
    }
}