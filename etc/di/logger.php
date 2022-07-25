<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use WyriHaximus\Monolog\Factory;
use WyriHaximus\React\PSR3\Stdio\StdioLogger;

use function DI\env;
use function DI\factory;

use function DI\get;
use const WyriHaximus\Constants\Boolean\TRUE_;

return (static fn (): array => [
    LoggerInterface::class => factory(static function (Logger $logger): LoggerInterface {
        return $logger;
    }),
    Logger::class => factory(static function (
        string $version,
        array $handlers,
        ?string $k8sPodName,
        ?string $k8sNamespace,
    ): Logger {
        $logger = Factory::create('', StdioLogger::create()->withHideLevel(TRUE_), [
            'version' => $version,
            'k8s_pod_name' => $k8sPodName,
            'k8s_namespace' => $k8sNamespace,
        ]);

        foreach ($handlers as $handler) {
            $logger->pushHandler($handler);
        }

        return $logger;
    })->
        parameter('version', env('APP_VERSION', 'dev-' . time()))->
        parameter('k8sPodName', env('K8S_POD_NAME'))->
        parameter('k8sNamespace', env('K8S_NAMESPACE'))->
        parameter('handlers', get('mammatus.logger.handlers')),
])();
