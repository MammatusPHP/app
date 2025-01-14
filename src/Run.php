<?php

declare(strict_types=1);

namespace Mammatus;

use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Throwable;

use function React\Async\async;

final readonly class Run
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /** @param callable(): ExitCode $boot */
    public function execute(callable $boot, mixed ...$args): ExitCode
    {
        $exitCode = ExitCode::ContingencyFailure;
        /** @var Deferred<ExitCode> $deferred */
        $deferred = new Deferred();
        Loop::futureTick(static fn (): PromiseInterface => async($boot)(...$args)->then($deferred->resolve(...), $deferred->reject(...)));
        $deferred->promise()->then(static function (ExitCode $resultingExitCode) use (&$exitCode): void {
            $exitCode = $resultingExitCode;
        }, function (Throwable $throwable) use (&$exitCode): void {
            $this->logger->emergency($throwable->getMessage());
            $exitCode = ExitCode::Failure;
        });

        $this->run($this->logger);
        $this->logger->debug('Execution completed with exit code: ' . $exitCode->name);

        return $exitCode;
    }

    public function run(LoggerInterface $logger): void
    {
        Loop::futureTick(static fn () => $logger->debug('Loop execution running'));
        $logger->debug('Loop execution starting');
        Loop::run();
        $logger->debug('Loop execution ended');
    }
}
