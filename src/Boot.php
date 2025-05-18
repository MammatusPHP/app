<?php

declare(strict_types=1);

namespace Mammatus;

use Mammatus\Boot\FallBackToEchoWhenEventLoopCompletesItsLoop;
use Mammatus\Contracts\Argv;
use Mammatus\Contracts\Bootable;
use Mammatus\LifeCycleEvents\Initialize;
use Psr\EventDispatcher\EventDispatcherInterface;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use Throwable;

use function assert;
use function React\Async\async;

/** @template T of Argv */
final readonly class Boot
{
    // phpcs:disable
    /**
     * @param class-string<Bootable<T>> $class
     * @param T                         $argv
     */
    public static function boot(string $class, Argv $argv): ExitCode
    {
        $container = ContainerFactory::create();

        $logger = $container->get(FallBackToEchoWhenEventLoopCompletesItsLoop::class);
        assert($logger instanceof FallBackToEchoWhenEventLoopCompletesItsLoop);

        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        assert($eventDispatcher instanceof EventDispatcherInterface);
        $eventDispatcher->dispatch(new Initialize());

        Loop::futureTick(async(static function () use ($logger, $eventDispatcher, $class): void {
            $logger->debug('Booting');
            if ($class === App::class) {
                $eventDispatcher->dispatch(new \Mammatus\LifeCycleEvents\Boot());
            } else {
                $eventDispatcher->dispatch(new \Mammatus\LifeCycleEvents\Start());
            }
        }));

        Loop::futureTick(async(static fn () => $logger->debug('Loop execution running')));

        $exitCode = ExitCode::ContingencyFailure;
        /** @var Deferred<ExitCode> $deferred */
        $deferred = new Deferred();
        Loop::futureTick(async(static function () use ($container, $class, $argv, $deferred): bool {
            try {
                /** @var Bootable<T> $bootable */
                $bootable = $container->get($class);
                $deferred->resolve($bootable->boot($argv));

                return true;
            } catch (Throwable $error) {
                $deferred->reject($error);

                return false;
            }
        }));
        $deferred->promise()->then(static function (ExitCode $resultingExitCode) use (&$exitCode): void {
            $exitCode = $resultingExitCode;
        }, static function (Throwable $throwable) use (&$exitCode, $logger): void {
            /**
             * Ignoring this because we're just passing it along
             *
             * @phpstan-ignore psr3.interpolated
             */
            $logger->emergency($throwable->getMessage());
            $exitCode = ExitCode::Failure;
        });

        $logger->debug('Loop execution starting');
        Loop::run();
        $logger->eventLoopDone();
        $logger->debug('Loop execution ended');
        $logger->debug('Execution completed with exit code: {exitCode}', ['exitCode' => $exitCode->value]);

        return $exitCode;
    }
}
