<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\App;
use Mammatus\App\Group;
use Mammatus\DevApp\Handler;
use Mammatus\ExitCode;
use Mammatus\Groups\Groups;
use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\Run;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerInterface;
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
        $container = Mockery::mock(ContainerInterface::class);
        $container->expects('get')->with(Handler::class)->atLeast()->once()->andReturn(new Handler());
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $eventDispatcher->expects('dispatch')->with(self::isInstanceOf(Initialize::class))->atLeast()->once();
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('log')->with(self::isString(), self::isString(), self::isArray())->times(6);
        $app = new App($eventDispatcher, $logger, new Run($logger), new Groups($container));
        self::assertSame(ExitCode::Success, $app->boot(new Group('groep')));
        self::assertSame(ExitCode::Failure, $app->boot(new Group('groep')));
    }
}
