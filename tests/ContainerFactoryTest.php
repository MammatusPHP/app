<?php

declare(strict_types=1);

namespace Mammatus\Tests;

use Mammatus\Container\Factory;
use Monolog\Logger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use WyriHaximus\Broadcast\ContainerListenerProvider;

final class ContainerFactoryTest extends TestCase
{
    #[Test]
    public function config(): void
    {
        $container = Factory::create();
        self::assertInstanceOf(ContainerListenerProvider::class, $container->get(ContainerListenerProvider::class));
        self::assertInstanceOf(EventDispatcherInterface::class, $container->get(EventDispatcherInterface::class));
        self::assertInstanceOf(LoggerInterface::class, $container->get(LoggerInterface::class));
        self::assertInstanceOf(Logger::class, $container->get(Logger::class));
        self::assertIsString($container->get('config.mammatus.random'));
    }
}
