<?php

declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\LifeCycleEvents\Initialize;
use React\EventLoop\Loop;
use WyriHaximus\Broadcast\Contracts\Listener;

use const STDERR;
use const STDIN;
use const STDOUT;

final class Output implements Listener
{
    public function handle(Initialize $event): void
    {
        ($this)();
    }

    public function __invoke(): void
    {
        // Remove STD* streams from loop on shutdown
        Loop::addTimer(4.9, function (): void {
            Loop::removeReadStream(STDIN);
            Loop::removeWriteStream(STDOUT);
            Loop::removeWriteStream(STDERR);
        });
    }
}
