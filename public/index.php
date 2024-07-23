<?php

use Web\Framework\Http\Kernel;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';



$request = \Web\Framework\Http\Request::createFromGlobals();

/** @var \League\Container\Container $container */
$container = require BASE_PATH . '/config/services.php';

$eventDispatcher = $container->get(\Web\Framework\Event\EventDispatcher::class);
$eventDispatcher
    ->addListener(\Web\Framework\Http\Events\ResponseEvent::class,
    new \App\Listeners\InternalErrorListener())
    ->addListener(\Web\Framework\Http\Events\ResponseEvent::class,
                 new \App\Listeners\ContentLengthListener)
    ->addListener(\Web\Framework\Dbal\Event\EntityPersist::class,
                        new \App\Listeners\HandleEntityListener());
$kernel = $container->get(Kernel::class);
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);