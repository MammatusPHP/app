<?php

declare(strict_types=1);

namespace Mammatus\Boot;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;
use WyriHaximus\PSR3\Utils;

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
    public function log($level, Stringable|string $message, array $context = []): void
    {
        if (! $this->eventLoopStopped) {
            /**
             * Ignoring this because we're just passing it along
             *
             * @phpstan-ignore psr3.interpolated
             */
            $this->logger->log($level, $message, $context);

            return;
        }

        /** @phpstan-ignore cast.string,argument.type */
        echo strtoupper((string) $level), ' ', Utils::processPlaceHolders((string) $message, $context), PHP_EOL;
    }

    public function eventLoopDone(): void
    {
        $this->eventLoopStopped = true;
    }
}
