<?php

declare(strict_types=1);

namespace Mammatus;

use Mammatus\App\Nothing;
use Mammatus\Contracts\Argv;
use Mammatus\Contracts\Bootable;
use Mammatus\LifeCycleEvents\Boot;
use Mammatus\LifeCycleEvents\Initialize;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;

use const WyriHaximus\Constants\Boolean\FALSE_;
use const WyriHaximus\Constants\Boolean\TRUE_;

/** @implements Bootable<Nothing> */
final class App implements Bootable
{
    private readonly LoggerInterface $logger;

    private bool $booted = FALSE_;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        private readonly Run $run,
    ) {
        $this->logger = new ContextLogger($logger, [], 'app');
    }

    public function boot(Argv $nothing): ExitCode
    {
        if ($this->booted === TRUE_) {
            $this->logger->emergency('Can\'t be booted twice');

            return ExitCode::Failure;
        }

        $this->eventDispatcher->dispatch(new Initialize());

        $this->booted = TRUE_;
        $this->logger->debug('Booting');

        $this->eventDispatcher->dispatch(new Boot());

        $exitCode = ExitCode::Success;
        $this->run->run($this->logger);
        $this->logger->debug('Execution completed with exit code: {exitCode}', ['exitCode' => $exitCode->value]);

        return $exitCode;
    }
}
