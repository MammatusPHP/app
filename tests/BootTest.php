<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\App;
use Mammatus\App\Group;
use Mammatus\Boot;
use Mammatus\ExitCode;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

final class BootTest extends TestCase
{
    #[Test]
    public function bootWithAppClass(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->expects('futureTick')->withArgs(static function (callable $listener): bool {
            $listener();

            return true;
        })->atLeast()->once();
        $loop->expects('run')->atLeast()->once();
        $loop->allows('addWriteStream');
        $loop->allows('addReadStream');
        $loop->allows('removeWriteStream');
        $loop->allows('removeReadStream');
        $loop->allows('addTimer');
        $loop->allows('addPeriodicTimer');
        $loop->allows('removeTimer');
        $loop->allows('cancelTimer');
        $loop->allows('addSignal');
        $loop->allows('removeSignal');
        $loop->allows('tick');
        $loop->allows('getStreamHash');

        /** @phpstan-ignore argument.type */
        $result = Boot::boot(App::class, new Group('group'));

        self::assertSame(ExitCode::Success, $result);
    }
}
