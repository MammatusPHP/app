<?php declare(strict_types=1);

use Psr\Log\LoggerInterface;
use ReactParallel\Factory;
use ReactParallel\ObjectProxy\Configuration;
use ReactParallel\ObjectProxy\Generated\ProxyList;
use ReactParallel\ObjectProxy\Proxy;
use ReactParallel\ObjectProxy\ProxyListInterface;
use WyriHaximus\Metrics\Registry;
use WyriHaximus\PSR3\CallableThrowableLogger\CallableThrowableLogger;

return [
    ProxyListInterface::class => static fn (): ProxyListInterface => new ProxyList(),
    Configuration::class => static fn (Factory $factory, Registry $registry, ProxyListInterface $proxyList): Configuration => (new Configuration($factory))->withProxyList($proxyList)->withMetrics(Configuration\Metrics::create($registry)),
    Proxy::class => static function (Configuration $configuration, Registry $registry) {
        $proxy = new Proxy($configuration);
        $proxy->share($registry, Registry::class);

        return $proxy;
    },
];
