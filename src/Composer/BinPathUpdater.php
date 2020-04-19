<?php declare(strict_types=1);

namespace Mammatus\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use function dirname;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\sprintf;
use function var_export;
use const DIRECTORY_SEPARATOR;

final class BinPathUpdater implements PluginInterface, EventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [ScriptEvents::PRE_AUTOLOAD_DUMP => 'updateBinPath'];
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
        // does nothing, see getSubscribedEvents() instead.
    }

    /**
     * Called before every dump autoload, generates a fresh PHP class.
     */
    public static function updateBinPath(Event $event): void
    {
        $autoloaderPath = $event->getComposer()->getConfig()->get('vendor-dir') . DIRECTORY_SEPARATOR . 'autoload.php';
        $binPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mammatus';

        file_put_contents(
            $binPath,
            sprintf(
                file_get_contents(
                    $binPath . '.source'
                ),
                $autoloaderPath,
            ),
        );
    }
}
