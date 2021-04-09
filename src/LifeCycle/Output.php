<?php

declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\LifeCycleEvents\Initialize;
use React\EventLoop\LoopInterface;
use WyriHaximus\Broadcast\Contracts\Listener;

use const STDERR;
use const STDIN;
use const STDOUT;

final class Output implements Listener
{
    private LoopInterface $loop;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function handle(Initialize $event): void
    {
        ($this)();
    }

    public function __invoke(): void
    {
        // Remove STD* streams from loop on shutdown
        $this->loop->addTimer(4.9, function (): void {
            $this->loop->removeReadStream(STDIN);
            $this->loop->removeWriteStream(STDOUT);
            $this->loop->removeWriteStream(STDERR);
        });
    }
}
