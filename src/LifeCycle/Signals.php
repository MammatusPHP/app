<?php

declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\LifeCycleEvents\Shutdown;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use WyriHaximus\Broadcast\Contracts\Listener;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;

use const SIGINT;
use const SIGTERM;

final readonly class Signals implements Listener
{
    private const array SIGNALS = [
        SIGTERM => 'SIGTERM',
        SIGINT => 'SIGINT',
    ];

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, private EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = new ContextLogger(
            $logger,
            ['listener' => 'signals'],
            'signals',
        );
    }

    public function handle(Initialize $event): void
    {
        ($this)();
    }

    public function __invoke(): void
    {
        $this->logger->debug('Setting up');

        $handler = function (int $caughtSignal) use (&$handler): void {
            $caughtSName = self::SIGNALS[$caughtSignal] ?? 'unknown signal';
            $this->logger->debug('Caught', ['signal' => $caughtSName]);
            $this->eventDispatcher->dispatch(new Shutdown());
            $this->logger->notice('Shutdown issued');

            foreach (self::SIGNALS as $signal => $signalName) {
                Loop::removeSignal($signal, $handler);
                $this->logger->debug('Removed signal listener', ['signal' => $signalName]);
            }
        };

        foreach (self::SIGNALS as $signal => $signalName) {
            Loop::addSignal($signal, $handler);
            $this->logger->debug('Added signal listener', ['signal' => $signalName]);
        }
    }
}
