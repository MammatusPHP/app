<?php

declare(strict_types=1);

namespace Mammatus;

use Mammatus\LifeCycleEvents\Boot;
use Mammatus\LifeCycleEvents\Initialize;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;

use const WyriHaximus\Constants\Boolean\FALSE_;
use const WyriHaximus\Constants\Boolean\TRUE_;

final class App
{
    private LoopInterface $loop;

    private EventDispatcherInterface $eventDispatcher;

    private LoggerInterface $logger;

    private bool $booted = FALSE_;

    public function __construct(LoopInterface $loop, EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->loop            = $loop;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger          = new ContextLogger($logger, [], 'app');
    }

    public function boot(): int
    {
        if ($this->booted === TRUE_) {
            $this->logger->emergency('Can\'t be booted twice');

            return ExitCode::FAILURE;
        }

        $this->eventDispatcher->dispatch(new Initialize());

        $this->booted = TRUE_;
        $this->logger->debug('Booting');

        $this->eventDispatcher->dispatch(new Boot());

        $exitCode = ExitCode::SUCCESS;
        $this->loop->futureTick(function (): void {
            $this->logger->debug('Loop execution running');
        });
        $this->logger->debug('Loop execution starting');
        $this->loop->run();
        $this->logger->debug('Loop execution ended');
        $this->logger->debug('Execution completed with exit code: ' . $exitCode);

        return $exitCode;
    }
}
