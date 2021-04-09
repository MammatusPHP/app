<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Signals;
use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\LifeCycleEvents\Shutdown;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

use const SIGINT;

/**
 * @internal
 */
final class SignalsTest extends TestCase
{
    /**
     * @test
     */
    public function runThrough(): void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(Argument::type('string'), Argument::type('string'), Argument::type('array'))->shouldBeCalledTimes(11);
        $logger->log('debug', '[signals] Caught', ['listener' => 'signals', 'signal' => 'SIGINT'])->shouldBeCalledTimes(2);

        $loop = $this->prophesize(LoopInterface::class);
        $loop->addSignal(Argument::type('int'), Argument::that(static function (callable $listener): bool {
            $listener(SIGINT);

            return true;
        }))->shouldBeCalled();
        $loop->removeSignal(Argument::type('int'), Argument::type('callable'))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(Shutdown::class))->shouldBeCalled();

        (new Signals($logger->reveal(), $loop->reveal(), $eventDispatcher->reveal()))->handle(new Initialize());
    }
}
