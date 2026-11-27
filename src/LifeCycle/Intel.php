<?php

declare(strict_types=1);

namespace Mammatus\LifeCycle;

use Mammatus\LifeCycleEvents\Kernel;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use WyriHaximus\Broadcast\Contracts\Listener;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;

use function get_class;
use function get_loaded_extensions;
use function implode;
use function preg_replace;
use function sort;

use const PHP_VERSION;
use const SORT_STRING;

final readonly class Intel implements Listener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = new ContextLogger(
            $logger,
            ['listener' => 'intel'],
            'intel',
        );
    }

    public function handle(Kernel $event): void
    {
        $extensions = get_loaded_extensions();
        sort($extensions, SORT_STRING);
        $extensionList = preg_replace('/, ([^,]+)$/', ', and $1', implode(', ', $extensions));

        $this->logger->debug('PHP version: {phpVersion}', ['phpVersion' => PHP_VERSION]);
        $this->logger->debug('PHP extensions: {extensions}', ['extensions' => $extensionList]);
        $this->logger->debug('Event loop: {eventLoop}', ['eventLoop' => get_class(Loop::get())]);
    }
}
