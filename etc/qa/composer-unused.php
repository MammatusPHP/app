<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    $config->addNamedFilter(NamedFilter::fromString('mammatus/healthz-vhost'));
    $config->addNamedFilter(NamedFilter::fromString('mammatus/http-server'));
    $config->addNamedFilter(NamedFilter::fromString('mammatus/http-server-attributes'));
    $config->addNamedFilter(NamedFilter::fromString('mammatus/http-server-contracts'));
    $config->addNamedFilter(NamedFilter::fromString('mammatus/http-server-webroot'));
    $config->addNamedFilter(NamedFilter::fromString('nikic/fast-route'));
    $config->addNamedFilter(NamedFilter::fromString('wyrihaximus/metrics'));
    $config->addNamedFilter(NamedFilter::fromString('wyrihaximus/metrics-tactician'));

    return $config;
};
