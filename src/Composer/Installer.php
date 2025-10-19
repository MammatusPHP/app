<?php

declare(strict_types=1);

namespace Mammatus\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Mammatus\Container\Factory;
use Throwable;

use function microtime;
use function round;

final class Installer implements PluginInterface, EventSubscriberInterface
{
    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [ScriptEvents::POST_AUTOLOAD_DUMP => 'buildContainer'];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        // does nothing, see getSubscribedEvents() instead.
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // does nothing, see getSubscribedEvents() instead.
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // does nothing, see getSubscribedEvents() instead.
    }

    /** @phpstan-ignore shipmonk.deadMethod */
    public static function buildContainer(Event $event): void
    {
        $start = microtime(true);
        $event->getIO()->write('<info>mammatus/app:</info> Generating PSR-11 container');
        try {
            Factory::create();
            $event->getIO()->write('<info>mammatus/app:</info> Generated PSR-11 container in ' . round(microtime(true) - $start, 2) . ' seconds');
        } catch (Throwable $e) {
            $event->getIO()->write('<error>mammatus/app:</error> Failed to generate PSR-11 container: ' . $e->getMessage());
        }
    }
}
