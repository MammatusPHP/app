<?php declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\App;
use Mammatus\ExitCode;
use Mammatus\LifeCycleEvents\Boot;
use Mammatus\LifeCycleEvents\Initialize;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

/**
 * @internal
 */
final class AppTest extends TestCase
{
    /**
     * @test
     */
    public function runThrough(): void
    {
        $loop = $this->prophesize(LoopInterface::class);
        $loop->futureTick(Argument::that(static function (callable $listener): bool {
            $listener();

            return true;
        }))->shouldBeCalled();
        $loop->run()->shouldBeCalled();
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(Initialize::class))->shouldBeCalled();
        $eventDispatcher->dispatch(Argument::type(Boot::class))->shouldBeCalled();
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(Argument::type('string'), Argument::type('string'), Argument::type('array'))->shouldBeCalledTimes(6);
        $app = new App($loop->reveal(), $eventDispatcher->reveal(), $logger->reveal());
        self::assertSame(ExitCode::SUCCESS, $app->boot());
        self::assertSame(ExitCode::FAILURE, $app->boot());
    }
}
