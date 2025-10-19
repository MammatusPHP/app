<?php

declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\Container\Factory;
use Mammatus\LifeCycleEvents\Build;
use Psr\Log\LoggerInterface;
use WyriHaximus\Broadcast\Contracts\Listener;

use function microtime;
use function round;

final readonly class Container implements Listener
{
    /** @phpstan-ignore shipmonk.deadMethod */
    public function __construct(private LoggerInterface $logger)
    {
    }

    /** @phpstan-ignore shipmonk.deadMethod */
    public function handle(Build $event): void
    {
        $start = microtime(true);
        $this->logger->info('Generating PSR-11 container');
        Factory::create();
        $this->logger->info('Generated PSR-11 container in ' . round(microtime(true) - $start, 2) . ' seconds');
    }
}
