<?php

declare(strict_types=1);

namespace Mammatus\Tests\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mammatus\Composer\ContainerCompiler;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

use function dirname;
use function is_file;

use const DIRECTORY_SEPARATOR;

final class InstallerTest extends TestCase
{
    #[Test]
    public function activateDoesNothing(): void
    {
        $installer = new ContainerCompiler();
        $composer  = Mockery::mock(Composer::class);
        $io        = Mockery::mock(IOInterface::class);

        $installer->activate($composer, $io);

        self::expectNotToPerformAssertions();
    }

    #[Test]
    public function deactivateDoesNothing(): void
    {
        $installer = new ContainerCompiler();
        $composer  = Mockery::mock(Composer::class);
        $io        = Mockery::mock(IOInterface::class);

        $installer->deactivate($composer, $io);

        self::expectNotToPerformAssertions();
    }

    #[Test]
    public function uninstallDoesNothing(): void
    {
        $installer = new ContainerCompiler();
        $composer  = Mockery::mock(Composer::class);
        $io        = Mockery::mock(IOInterface::class);

        $installer->uninstall($composer, $io);

        self::expectNotToPerformAssertions();
    }

    #[Test]
    public function buildContainerSuccess(): void
    {
        $io = Mockery::mock(IOInterface::class);
        $io->expects('write')->with('<info>mammatus/app:</info> Generating PSR-11 container')->once();
        $io->expects('write')->with(self::stringStartsWith('<info>mammatus/app:</info> Generated PSR-11 container in'))->once();

        $event = Mockery::mock(Event::class);
        $event->expects('getIO')->andReturn($io)->atLeast()->once();

        ContainerCompiler::buildContainer($event);

        $containerFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'MammatusGeneratedCompiledContainer.php';
        self::assertTrue(is_file($containerFile), 'Generated container file should exist');
    }
}
