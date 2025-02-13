<?php

declare(strict_types=1);

namespace BEAR\Package;

use BEAR\AppMeta\Meta;
use BEAR\Package\Injector\PackageInjector;
use Ray\Di\AbstractModule;
use Ray\Di\InjectorInterface;
use Ray\PsrCacheModule\LocalCacheProvider;
use Symfony\Contracts\Cache\CacheInterface;

use function str_replace;

/** @see PackageInjector */
final class Injector
{
    /** @codeCoverageIgnore */
    private function __construct()
    {
    }

    public static function getInstance(string $appName, string $context, string $appDir, CacheInterface|null $cache = null): InjectorInterface
    {
        $meta = new Meta($appName, $context, $appDir);
        $cacheNamespace = str_replace('\\', '_', $appName) . $context;
        $cache ??= (new LocalCacheProvider($meta->tmpDir . '/injector', $cacheNamespace))->get();

        return PackageInjector::getInstance($meta, $context, $cache);
    }

    public static function getOverrideInstance(string $appName, string $context, string $appDir, AbstractModule $overrideModule): InjectorInterface
    {
        return PackageInjector::factory(new Meta($appName, $context, $appDir), $context, $overrideModule);
    }
}
