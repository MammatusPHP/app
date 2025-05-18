<?php

declare(strict_types=1);

use Mammatus\Boot\FallBackToEchoWhenEventLoopCompletesItsLoop;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use WyriHaximus\Monolog\Factory;
use WyriHaximus\React\PSR3\Stdio\StdioLogger;

use function DI\env;
use function DI\factory;
use function DI\get;

use const WyriHaximus\Constants\Boolean\TRUE_;

return (static fn (): array => [
    FallBackToEchoWhenEventLoopCompletesItsLoop::class => factory(static fn (Logger $logger): FallBackToEchoWhenEventLoopCompletesItsLoop => new FallBackToEchoWhenEventLoopCompletesItsLoop($logger)),
    LoggerInterface::class => factory(static fn (FallBackToEchoWhenEventLoopCompletesItsLoop $logger): LoggerInterface => $logger),
    Logger::class => factory(static function (
        string $version,
        array $handlers,
        string|null $k8sPodName,
        string|null $k8sNamespace,
    ): Logger {
        $logger = Factory::create('', StdioLogger::create()->withHideLevel(TRUE_), [
            'version' => $version,
            'k8s_pod_name' => $k8sPodName,
            'k8s_namespace' => $k8sNamespace,
        ]);

        foreach ($handlers as $handler) {
            if (! ($handler instanceof HandlerInterface)) {
                continue;
            }

            $logger->pushHandler($handler);
        }

        return $logger;
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time()))->
        parameter('k8sPodName', env('K8S_POD_NAME', null))->
        parameter('k8sNamespace', env('K8S_NAMESPACE', null))->
        parameter('handlers', get('config.logger.handlers')),
])();
