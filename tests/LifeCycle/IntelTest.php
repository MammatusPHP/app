<?php

declare(strict_types=1);

namespace Mammatus\Tests\LifeCycle;

use Mammatus\LifeCycle\Intel;
use Mammatus\LifeCycleEvents\Initialize;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use WyriHaximus\TestUtilities\TestCase;

use function get_class;
use function get_loaded_extensions;
use function implode;
use function preg_replace;
use function sort;

use const PHP_VERSION;
use const SORT_STRING;

final class IntelTest extends TestCase
{
    #[Test]
    public function logsRuntimeInformation(): void
    {
        $extensions = get_loaded_extensions();
        sort($extensions, SORT_STRING);
        $extensionList = preg_replace('/, ([^,]+)$/', ', and $1', implode(', ', $extensions));

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('log')->with('debug', '[intel] PHP version: {phpVersion}', ['listener' => 'intel', 'phpVersion' => PHP_VERSION])->once();
        $logger->expects('log')->with('debug', '[intel] PHP extensions: {extensions}', ['listener' => 'intel', 'extensions' => $extensionList])->once();
        $logger->expects('log')->with('debug', '[intel] Event loop: {eventLoop}', ['listener' => 'intel', 'eventLoop' => get_class(Loop::get())])->once();

        new Intel($logger)->handle(new Initialize());
    }
}
