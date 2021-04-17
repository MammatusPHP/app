<?php

declare(strict_types=1);

use ReactParallel\Factory;
use ReactParallel\ObjectProxy\Configuration;
use ReactParallel\ObjectProxy\Generated\ProxyList;
use ReactParallel\ObjectProxy\Proxy;
use ReactParallel\ObjectProxy\ProxyListInterface;
use ReactParallel\Psr11ContainerProxy\CustomOverridesListInterface;
use ReactParallel\Psr11ContainerProxy\Generated\CustomOverridesList;
use WyriHaximus\Metrics\Registry;

return [
    ProxyListInterface::class => static fn(ProxyList $proxyList): ProxyListInterface => $proxyList,
    CustomOverridesListInterface::class => static fn(CustomOverridesList $customOverridesList): CustomOverridesListInterface => $customOverridesList,
    Proxy::class => static function (Factory $factory, Registry $registry) {
        $proxy = (new Proxy((new Configuration($factory))->withMetrics(Configuration\Metrics::create($registry))));
        $proxy->create($registry, Registry::class, true);

        return $proxy;
    },
];
