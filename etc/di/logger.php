<?php declare(strict_types=1);

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Monolog\Logger;
use Monolog\Processor;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\Monolog\FormattedPsrHandler\FormattedPsrHandler;
use WyriHaximus\Monolog\Processors\ExceptionClassProcessor;
use WyriHaximus\Monolog\Processors\KeyValueProcessor;
use WyriHaximus\Monolog\Processors\RuntimeProcessor;
use WyriHaximus\Monolog\Processors\ToContextProcessor;
use WyriHaximus\Monolog\Processors\TraceProcessor;
use WyriHaximus\React\PSR3\Stdio\StdioLogger;
use function DI\env;
use function DI\factory;
use const WyriHaximus\Constants\Boolean\FALSE_;
use const WyriHaximus\Constants\Boolean\TRUE_;

return (static function (): array {
    return [
        LoggerInterface::class => factory(static function (Logger $logger): LoggerInterface {
            return $logger;
        }),
        Logger::class => factory(static function (
            LoopInterface $loop,
            string $version
            //            array $processors,
            //            array $handlers
        ): Logger {
            $logger = new Logger('');
            $logger->pushProcessor(new ToContextProcessor());
            $logger->pushProcessor(new TraceProcessor());
            $logger->pushProcessor(new KeyValueProcessor('version', $version));
            $logger->pushProcessor(new ExceptionClassProcessor());
            $logger->pushProcessor(new RuntimeProcessor());
            $logger->pushProcessor(new Processor\ProcessIdProcessor());
            $logger->pushProcessor(new Processor\IntrospectionProcessor(Logger::NOTICE));
            $logger->pushProcessor(new Processor\MemoryUsageProcessor());
            $logger->pushProcessor(new Processor\MemoryPeakUsageProcessor());
//            foreach ($processors as $processor) {
//                $logger->pushProcessor($processor);
//            }

            $consoleHandler = new FormattedPsrHandler(StdioLogger::create($loop)->withHideLevel(TRUE_));
            $consoleHandler->setFormatter(new ColoredLineFormatter(
                null,
                '[%datetime%] %channel%.%level_name%: %message%',
                'Y-m-d H:i:s.u',
                TRUE_,
                FALSE_
            ));
            $logger->pushHandler($consoleHandler);
//            foreach ($handlers as $handler) {
//                $logger->pushHandler($handler);
//            }

            return $logger;
        })->
            parameter('version', env('APP_VERSION', 'dev-' . time())),
//            parameter('handlers', get('config.logger.handlers'))->
//            parameter('processors', get('config.logger.processors')),
    ];
})();
