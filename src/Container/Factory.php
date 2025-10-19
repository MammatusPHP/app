<?php

declare(strict_types=1);

namespace Mammatus\Container;

use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionArray;
use Mammatus\ConfigurationLocator;
use PHPDIDefinitions\DefinitionsGatherer;
use Psr\Container\ContainerInterface;

use function dirname;

use const DIRECTORY_SEPARATOR;

final class Factory
{
    private const string CONTAINER_CLASS = 'MammatusGeneratedCompiledContainer';

    public static function create(): ContainerInterface
    {
        /** @phpstan-ignore argument.type */
        $container = new ContainerBuilder(self::CONTAINER_CLASS);

        $container->useAutowiring(true);
        $container->useAttributes(true);
        $config = Defaults::create();
        foreach (ConfigurationLocator::locate() as $key => $value) {
            $config['config.' . $key] = $value;
        }

        $container->addDefinitions(new DefinitionArray($config));
        $container->addDefinitions(...DefinitionsGatherer::gather());
        $container->enableCompilation(
            dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'Generated',
            self::CONTAINER_CLASS,
        );

        return $container->build();
    }
}
