<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use ReactParallel\ObjectProxy\Proxy;
use WyriHaximus\Monolog\Factory;

use function DI\env;
use function DI\factory;

return (static fn (): array => [
    LoggerInterface::class => factory(static function (
        Proxy $proxy,
        string $version
    ): LoggerInterface {
        return $proxy->thread(
            Factory::create(
                '',
                new Logger('', [new StreamHandler('php://stdout')]),
                ['version' => $version]
            ),
            LoggerInterface::class
        );
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time())),
])();
