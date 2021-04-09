<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use ReactParallel\ObjectProxy\Proxy;
use WyriHaximus\Monolog\Factory;

use WyriHaximus\PSR3\CallableThrowableLogger\CallableThrowableLogger;
use function DI\env;
use function DI\factory;

return (static fn (): array => [
    LoggerInterface::class => factory(static function (
        Proxy $proxy,
        string $version
    ): LoggerInterface {
        $logger = $proxy->thread(
            Factory::create(
                '',
                new Logger('', [new StreamHandler('php://stdout')]),
                ['version' => $version]
            ),
            LoggerInterface::class
        );

        $proxy->on('error', CallableThrowableLogger::create($logger));

        return $logger;
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time())),
])();
