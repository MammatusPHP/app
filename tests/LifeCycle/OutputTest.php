<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Output;
use Mammatus\LifeCycleEvents\Initialize;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

use const STDERR;
use const STDIN;
use const STDOUT;

final class OutputTest extends TestCase
{
    #[Test]
    public function runThrough(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('addTimer')->withArgs(static function (float $interval, callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('removeReadStream')->with(STDIN)->once();
        $loop->expects('removeWriteStream')->with(STDOUT)->once();
        $loop->expects('removeWriteStream')->with(STDERR)->once();

        (new Output())->handle(new Initialize());
    }
}
