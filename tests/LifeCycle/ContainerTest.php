<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Container;
use Mammatus\LifeCycleEvents\Build;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use WyriHaximus\TestUtilities\TestCase;

final class ContainerTest extends TestCase
{
    #[Test]
    public function handleCreatesContainerAndLogs(): void
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('info')->with('Generating PSR-11 container')->once();
        $logger->expects('info')->with(self::stringStartsWith('Generated PSR-11 container in'))->once();

        $container = new Container($logger);
        $container->handle(new Build());
    }
}
