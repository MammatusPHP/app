<?php

declare(strict_types=1);

namespace Mammatus\Boot;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

use function strtoupper;

use const PHP_EOL;

final class FallBackToEchoWhenEventLoopCompletesItsLoop extends AbstractLogger implements LoggerInterface
{
    private bool $eventLoopStopped = false;

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * @param array<mixed> $context
     *
     * @inheritDoc
     */
    public function log($level, Stringable|string $message, array $context = [])
    {
        if ($this->eventLoopStopped !== true) {
            $this->logger->log($level, $message, $context);

            return;
        }

        /** @phpstan-ignore-next-line */
        echo strtoupper((string) $level), ' ', $message, PHP_EOL;
    }

    public function eventLoopDone(): void
    {
        $this->eventLoopStopped = true;
    }
}
