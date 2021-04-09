<?php declare(strict_types=1);

use WyriHaximus\Metrics\Configuration;
use WyriHaximus\Metrics\InMemory\Registry as InMemoryRegistry;
use WyriHaximus\Metrics\Registry;

return [
    Registry::class => new InMemoryRegistry(Configuration::create()),
];
