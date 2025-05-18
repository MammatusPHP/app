<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Signals;
use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\LifeCycleEvents\Shutdown;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

final class SignalsTest extends TestCase
{
    #[Test]
    public function runThrough(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('log')->with('debug', '[signals] Caught', ['listener' => 'signals', 'signal' => 'SIGINT'])->atLeast()->once();
        $logger->expects('log')->with(self::isString(), self::isString(), self::isArray())->times(10);

        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('addSignal')->withArgs(static function (int $signal, callable $listener): bool {
            $listener($signal);

            return true;
        })->atLeast()->once();
        $loop->expects('removeSignal')->with(self::isInt(), self::isCallable())->atLeast()->once();

        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher->expects('dispatch')->with(self::isInstanceOf(Shutdown::class))->atLeast()->once();

        (new Signals($logger, $eventDispatcher))->handle(new Initialize());
    }
}
