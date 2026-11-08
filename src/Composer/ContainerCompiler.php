<?php

declare(strict_types=1);

namespace Mammatus\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Mammatus\Container\Factory;
use Throwable;

use function dirname;
use function is_file;
use function microtime;
use function round;
use function unlink;

use const DIRECTORY_SEPARATOR;

final class ContainerCompiler implements PluginInterface
{
    public static function buildContainer(Event $event): void
    {
        $containerFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'MammatusGeneratedCompiledContainer.php';
        if (is_file($containerFile)) {
            unlink($containerFile);
        }

        $start = microtime(true);
        $event->getIO()->write('<info>mammatus/app:</info> Generating PSR-11 container');
        try {
            Factory::create();
            $event->getIO()->write('<info>mammatus/app:</info> Generated PSR-11 container in ' . round(microtime(true) - $start, 2) . ' seconds');
        } catch (Throwable $e) {
            $event->getIO()->write('<error>mammatus/app:</error> Failed to generate PSR-11 container: ' . $e->getMessage());
        }
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}
