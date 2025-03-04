<?php

namespace Mammatus\Boot;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class EventLoopDoneLoggerSpecialUseCaseHandler extends AbstractLogger implements LoggerInterface
{
    private bool $eventLoopStopped = false;

    public function __construct(private readonly LoggerInterface $logger)
    {

    }

    /**
     * @inheritDoc
     */
    public function log($level, \Stringable|string $message, array $context = [])
    {
        if ($this->eventLoopStopped !== true) {
            $this->logger->log($level, $message, $context);
            return;
        }

        echo strtoupper($level), ' ', $message, PHP_EOL;
    }

    public function eventLoopDone(): void
    {
        $this->eventLoopStopped = true;
    }
}
