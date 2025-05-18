<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use WyriHaximus\Broadcast\ContainerListenerProvider;
use WyriHaximus\Broadcast\Dispatcher;
use WyriHaximus\PSR3\ContextLogger\ContextLogger;

use function DI\factory;

return (static fn (): array => [
    EventDispatcherInterface::class => factory(static fn (LoggerInterface $logger, ContainerListenerProvider $listenerProvider): Dispatcher => new Dispatcher(
        $listenerProvider,
        new ContextLogger(
            $logger,
            ['component' => 'event-dispatcher'],
            'event-dispatcher',
        ),
    )),
])();
