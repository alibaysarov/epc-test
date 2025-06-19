<?php

require_once __DIR__.'/vendor/autoload.php';

use App\DI\Container;
use App\DI\Foo;
use App\Service\NotificationService;
use App\Service\NotificationServiceInterface;

$testContainer = new Container();
$testContainer->set(
  NotificationServiceInterface::class,
  NotificationService::class
);

$service = $testContainer->get(Foo::class);
$service->sayHi();
$service->processHandler();

$parsedUrl = parse_url(
  "https://www.example.com/category/products.php?id=123&sort=desc&filter[price]=low&search_query=product+name&user_id=7890&_sid=abc123xyz"
);
$customParsedUrl = custom_parse_url(
  "https://www.example.com/category.php?id=123&sort=desc&filter[price]=low&search_query=product+name&user_id=7890&_sid=abc123xyz"
);
dump($parsedUrl);


