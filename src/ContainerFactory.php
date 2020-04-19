<?php declare(strict_types=1);

namespace Mammatus;

use DI\ContainerBuilder;
use PHPDIDefinitions\DefinitionsGatherer;
use Psr\Container\ContainerInterface;
use function iterator_to_array;
use const WyriHaximus\Constants\Boolean\TRUE_;

final class ContainerFactory
{
    public static function create(): ContainerInterface
    {
        /** @psalm-suppress InvalidArgument */
        $definitions = iterator_to_array(DefinitionsGatherer::gather());
        $container   = new ContainerBuilder();
        $container->useAnnotations(TRUE_);
        foreach (ConfigurationLocator::locate() as $key => $value) {
            $definitions['config.' . $key] = $value;
        }

        $container->addDefinitions($definitions);

        return $container->build();
    }
}
