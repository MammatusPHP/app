<?php

declare(strict_types=1);

namespace Mammatus;

use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionArray;
use PHPDIDefinitions\DefinitionsGatherer;
use Psr\Container\ContainerInterface;

use const DIRECTORY_SEPARATOR;

final class ContainerFactory
{
    private const string CONTAINER_CLASS = 'MammatusGeneratedCompiledContainer';

    public static function create(): ContainerInterface
    {
        /** @phpstan-ignore argument.type */
        $container = new ContainerBuilder(self::CONTAINER_CLASS);

        $container->useAutowiring(true);
        $container->useAttributes(true);
        $config = self::configDefaults();
        foreach (ConfigurationLocator::locate() as $key => $value) {
            $config['config.' . $key] = $value;
        }

        $container->addDefinitions(new DefinitionArray($config));
        $container->addDefinitions(...DefinitionsGatherer::gather());
        $container->enableCompilation(
            __DIR__ . DIRECTORY_SEPARATOR . 'Generated',
            self::CONTAINER_CLASS,
        );

        return $container->build();
    }

    /** @return array<string, mixed> */
    private static function configDefaults(): array
    {
        return [
            'config.logger.handlers' => [],
        ];
    }
}
