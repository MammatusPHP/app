<?php

declare(strict_types=1);

namespace Mammatus;

use Mammatus\Container\Factory;
use Psr\EventDispatcher\EventDispatcherInterface;

use function assert;

final readonly class Build
{
    // phpcs:disable
    /**
     * @phpstan-ignore shipmonk.deadMethod
     */
    public static function build(): ExitCode
    {
        $container = Factory::create();

        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        assert($eventDispatcher instanceof EventDispatcherInterface);
        $eventDispatcher->dispatch(new \Mammatus\LifeCycleEvents\Build());

        return ExitCode::Success;
    }
}
