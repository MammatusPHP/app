<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\App;
use Mammatus\App\Nothing;
use Mammatus\ExitCode;
use Mammatus\LifeCycleEvents\Boot;
use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\Run;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

final class AppTest extends TestCase
{
    #[Test]
    public function runThrough(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->atLeast()->once();
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher->expects('dispatch')->with(self::isInstanceOf(Initialize::class))->atLeast()->once();
        $eventDispatcher->expects('dispatch')->with(self::isInstanceOf(Boot::class))->atLeast()->once();
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('log')->with(self::isString(), self::isString(), self::isArray())->times(6);
        $app = new App($eventDispatcher, $logger, new Run($logger));
        self::assertSame(ExitCode::Success, $app->boot(new Nothing()));
        self::assertSame(ExitCode::Failure, $app->boot(new Nothing()));
    }
}
