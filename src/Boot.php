<?php

declare(strict_types=1);

namespace Mammatus;

use Mammatus\Contracts\Argv;
use Mammatus\Contracts\Bootable;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use Throwable;

use function assert;
use function React\Async\async;

/** @template T of Argv */
final readonly class Boot
{
    /**
     * @param class-string<Bootable<T>> $class
     * @param T                         $argv
     */
    public static function boot(string $class, Argv $argv): ExitCode
    {
        $container = ContainerFactory::create();
        $logger    = $container->get(LoggerInterface::class);
        Loop::futureTick(static fn () => $logger->debug('Loop execution running'));
        assert($logger instanceof LoggerInterface);
        $exitCode = ExitCode::ContingencyFailure;
        /** @var Deferred<ExitCode> $deferred */
        $deferred = new Deferred();
        Loop::futureTick(async(static function () use ($container, $class, $argv, $deferred): void {
            try {
                /** @var Bootable<T> $bootable */
                $bootable = $container->get($class);
                $deferred->resolve($bootable->boot($argv));
            } catch (Throwable $error) {
                $deferred->reject($error);
            }
        }));
        $deferred->promise()->then(static function (ExitCode $resultingExitCode) use (&$exitCode): void {
            $exitCode = $resultingExitCode;
        }, static function (Throwable $throwable) use (&$exitCode, $logger): void {
            echo $throwable;
            $logger->emergency($throwable->getMessage());
            $exitCode = ExitCode::Failure;
        });

        $logger->debug('Loop execution starting');
        Loop::run();
        $logger->debug('Loop execution ended');
        $logger->debug('Execution completed with exit code: ' . $exitCode->name);

        return $exitCode;
    }
}
