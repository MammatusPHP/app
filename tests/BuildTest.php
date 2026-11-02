<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\Build;
use Mammatus\ExitCode;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use WyriHaximus\TestUtilities\TestCase;

final class BuildTest extends TestCase
{
    #[Test]
    public function buildDispatchesEventAndReturnsSuccess(): void
    {
        $loop = Mockery::mock(LoopInterface::class);
        /** @phpstan-ignore staticMethod.internal */
        Loop::set($loop);
        $loop->allows('addWriteStream');
        $loop->allows('addReadStream');
        $loop->allows('removeWriteStream');
        $loop->allows('removeReadStream');

        $result = Build::build();

        self::assertSame(ExitCode::Success, $result);
    }
}
