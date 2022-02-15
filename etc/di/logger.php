<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use WyriHaximus\Monolog\Factory;
use WyriHaximus\React\PSR3\Stdio\StdioLogger;

use function DI\env;
use function DI\factory;

use const WyriHaximus\Constants\Boolean\TRUE_;

return (static fn (): array => [
    LoggerInterface::class => factory(static function (Logger $logger): LoggerInterface {
        return $logger;
    }),
    Logger::class => factory(static function (
        string $version
    ): Logger {
        return Factory::create('', StdioLogger::create()->withHideLevel(TRUE_), ['version' => $version]);
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time())),
])();
