<?php

declare(strict_types=1);

namespace Mammatus\Tests\Boot;

use Mammatus\Boot\FallBackToEchoWhenEventLoopCompletesItsLoop;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use WyriHaximus\TestUtilities\TestCase;

use function ob_get_clean;
use function ob_start;

final class FallBackToEchoWhenEventLoopCompletesItsLoopTest extends TestCase
{
    #[Test]
    public function logDelegatesToInnerLoggerBeforeEventLoopDone(): void
    {
        $inner = Mockery::mock(LoggerInterface::class);
        $inner->expects('log')->with('info', 'test message', ['key' => 'value'])->once();

        $wrapper = new FallBackToEchoWhenEventLoopCompletesItsLoop($inner);
        $wrapper->log('info', 'test message', ['key' => 'value']);
    }

    #[Test]
    public function logEchoesAfterEventLoopDone(): void
    {
        $inner = Mockery::mock(LoggerInterface::class);
        $inner->expects('log')->never();

        $wrapper = new FallBackToEchoWhenEventLoopCompletesItsLoop($inner);
        $wrapper->eventLoopDone();

        $wrapper2 = new FallBackToEchoWhenEventLoopCompletesItsLoop(Mockery::mock(LoggerInterface::class));
        $wrapper2->eventLoopDone();
        ob_start();
        $wrapper2->log('error', 'test {message}', ['message' => 'hello']);
        $output = ob_get_clean();

        /** @phpstan-ignore argument.type */
        self::assertStringContainsString('ERROR', $output);
        /** @phpstan-ignore argument.type */
        self::assertStringContainsString('test hello', $output);
    }

    #[Test]
    public function eventLoopDoneTogglesBehavior(): void
    {
        $inner = Mockery::mock(LoggerInterface::class);
        $inner->expects('log')->with('debug', 'before', [])->once();

        $wrapper = new FallBackToEchoWhenEventLoopCompletesItsLoop($inner);
        $wrapper->log('debug', 'before', []);

        $inner2 = Mockery::mock(LoggerInterface::class);

        $wrapper2 = new FallBackToEchoWhenEventLoopCompletesItsLoop($inner2);
        $wrapper2->eventLoopDone();
        ob_start();
        $wrapper2->log('warning', 'after', []);
        $output = ob_get_clean();
        /** @phpstan-ignore argument.type */
        self::assertStringContainsString('WARNING', $output);
        /** @phpstan-ignore argument.type */
        self::assertStringContainsString('after', $output);
    }
}
