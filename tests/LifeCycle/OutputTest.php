<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Output;
use Mammatus\LifeCycleEvents\Initialize;
use Prophecy\Argument;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

use const STDERR;
use const STDIN;
use const STDOUT;

/** @internal */
final class OutputTest extends TestCase
{
    /** @test */
    public function runThrough(): void
    {
        $loop = $this->prophesize(LoopInterface::class);
        Loop::set($loop->reveal());
        $loop->addTimer(Argument::type('float'), Argument::that(static function (callable $listener): bool {
            $listener();

            return true;
        }))->shouldBeCalled();
        $loop->removeReadStream(STDIN)->shouldBeCalled();
        $loop->removeWriteStream(STDOUT)->shouldBeCalled();
        $loop->removeWriteStream(STDERR)->shouldBeCalled();

        (new Output())->handle(new Initialize());
    }
}
