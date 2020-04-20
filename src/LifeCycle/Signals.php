<?php declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\LifeCycleEvents\Initialize;
use Mammatus\LifeCycleEvents\Shutdown;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\Broadcast\Contracts\Listener;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;
use const SIGINT;
use const SIGTERM;

final class Signals implements Listener
{
    private const SIGNALS = [
        SIGTERM => 'SIGTERM',
        SIGINT => 'SIGINT',
    ];

    private LoggerInterface $logger;

    private LoopInterface $loop;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(LoggerInterface $logger, LoopInterface $loop, EventDispatcherInterface $eventDispatcher)
    {
        $this->logger          = new ContextLogger(
            $logger,
            ['listener' => 'signals'],
            'signals'
        );
        $this->loop            = $loop;
        $this->eventDispatcher = $eventDispatcher;
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
                $this->loop->removeSignal($signal, $handler);
                $this->logger->debug('Removed signal listener', ['signal' => $signalName]);
            }
        };

        foreach (self::SIGNALS as $signal => $signalName) {
            $this->loop->addSignal($signal, $handler);
            $this->logger->debug('Added signal listener', ['signal' => $signalName]);
        }
    }
}
