<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\ContainerFactory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use WyriHaximus\Broadcast\ContainerListenerProvider;

/**
 * @internal
 */
final class ContainerFactoryTest extends TestCase
{
    public function testConfig(): void
    {
        $container = ContainerFactory::create();
        self::assertInstanceOf(ContainerListenerProvider::class, $container->get(ContainerListenerProvider::class));
        self::assertInstanceOf(EventDispatcherInterface::class, $container->get(EventDispatcherInterface::class));
        self::assertInstanceOf(LoggerInterface::class, $container->get(LoggerInterface::class));
        self::assertInstanceOf(Logger::class, $container->get(Logger::class));
        self::assertIsString($container->get('config.mammatus.random'));
    }
}
