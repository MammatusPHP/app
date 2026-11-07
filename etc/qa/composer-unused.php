<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use Webmozart\Glob\Glob;

return static function (Configuration $config): Configuration {
    $config->addNamedFilter(NamedFilter::fromString('wyrihaximus/metrics-tactician'));

    // Add etc/ directory files for symbol scanning (excludes etc/qa/ which contains QA configs)
    $root = getcwd();
    $config->setAdditionalFilesFor(
        'mammatus/app',
        array_merge(
            Glob::glob($root . '/etc/config/**/*.php'),
            Glob::glob($root . '/etc/di/**/*.php'),
            Glob::glob($root . '/etc/dev-app/**/*.php'),
        ),
    );

    return $config;
};
