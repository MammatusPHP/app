<?php declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Output;
use Mammatus\LifeCycleEvents\Initialize;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use React\EventLoop\LoopInterface;
use const STDERR;
use const STDIN;
use const STDOUT;

/**
 * @internal
 */
final class OutputTest extends TestCase
{
    /**
     * @test
     */
    public function runThrough(): void
    {
        $loop = $this->prophesize(LoopInterface::class);
        $loop->addTimer(Argument::type('float'), Argument::that(static function (callable $listener): bool {
            $listener();

            return true;
        }))->shouldBeCalled();
        $loop->removeReadStream(STDIN)->shouldBeCalled();
        $loop->removeWriteStream(STDOUT)->shouldBeCalled();
        $loop->removeWriteStream(STDERR)->shouldBeCalled();

        (new Output($loop->reveal()))->handle(new Initialize());
    }
}
