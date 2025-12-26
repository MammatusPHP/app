<?php

declare(strict_types=1);

namespace Mammatus\DevApp;

use Mammatus\Groups\Contracts\LifeCycleHandler;

final class Handler implements LifeCycleHandler
{
    public static function group(): string
    {
        return 'groep';
    }

    public function start(): void
    {
        // TODO: Implement start() method.
    }

    public function stop(): void
    {
        // TODO: Implement stop() method.
    }
}
