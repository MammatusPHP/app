<?php declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
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
        LoopInterface $loop,
        string $version
    ): Logger {
        return Factory::create('', StdioLogger::create($loop)->withHideLevel(TRUE_), ['version' => $version]);
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time())),
])();
