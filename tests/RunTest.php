<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\ExitCode;
use Mammatus\Run;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use RuntimeException;
use WyriHaximus\TestUtilities\TestCase;

final class RunTest extends TestCase
{
    #[Test]
    public function executeSuccessPath(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->once();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('debug')->with('Loop execution starting')->once();
        $logger->expects('debug')->with('Loop execution running')->once();
        $logger->expects('debug')->with('Loop execution ended')->once();
        $logger->expects('debug')->with('Execution completed with exit code: {exitCode}', ['exitCode' => ExitCode::Success->value])->once();

        $run    = new Run($logger);
        $result = $run->execute(static fn () => ExitCode::Success);

        self::assertSame(ExitCode::Success, $result);
    }

    #[Test]
    public function executeFailurePath(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->once();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('emergency')->with('Failed executing with the following error: {message}', ['message' => 'test error'])->once();
        $logger->expects('debug')->with('Loop execution starting')->once();
        $logger->expects('debug')->with('Loop execution running')->once();
        $logger->expects('debug')->with('Loop execution ended')->once();
        $logger->expects('debug')->with('Execution completed with exit code: {exitCode}', ['exitCode' => ExitCode::Failure->value])->once();

        $run    = new Run($logger);
        $result = $run->execute(static function (): ExitCode {
            throw new RuntimeException('test error');
        });

        self::assertSame(ExitCode::Failure, $result);
    }

    #[Test]
    public function runLogsLifecycle(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->once();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('debug')->with('Loop execution starting')->once();
        $logger->expects('debug')->with('Loop execution running')->once();
        $logger->expects('debug')->with('Loop execution ended')->once();

        $run = new Run($logger);
        $run->run($logger);
    }

    #[Test]
    public function executeWithArgs(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->once();

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('debug')->with('Loop execution starting')->once();
        $logger->expects('debug')->with('Loop execution running')->once();
        $logger->expects('debug')->with('Loop execution ended')->once();
        $logger->expects('debug')->with('Execution completed with exit code: {exitCode}', ['exitCode' => ExitCode::Success->value])->once();

        $received = null;
        $run      = new Run($logger);
        /** @phpstan-ignore argument.type */
        $result = $run->execute(static function (string $arg) use (&$received): ExitCode {
            $received = $arg;

            return ExitCode::Success;
        }, 'test-arg');

        self::assertSame(ExitCode::Success, $result);
        self::assertSame('test-arg', $received);
    }
}
