<?php

declare(strict_types=1);

namespace Mammatus;

use DI\ContainerBuilder;
use PHPDIDefinitions\DefinitionsGatherer;
use Psr\Container\ContainerInterface;

use function iterator_to_array;

use const WyriHaximus\Constants\Boolean\TRUE_;

final class ContainerFactory
{
    /** @param array<string, mixed> $overrides */
    public static function create(array $overrides = []): ContainerInterface
    {
        /** @psalm-suppress InvalidArgument */
        $definitions = iterator_to_array(DefinitionsGatherer::gather());
        /** @psalm-suppress MixedAssignment */
        foreach ($overrides as $key => $value) {
            /** @psalm-suppress MixedAssignment */
            $definitions[$key] = $value;
        }

        $container = new ContainerBuilder();
        $container->useAttributes(TRUE_);
        foreach (ConfigurationLocator::locate() as $key => $value) {
            $definitions['config.' . $key] = $value;
        }

        $container->addDefinitions($definitions);

        return $container->build();
    }
}
