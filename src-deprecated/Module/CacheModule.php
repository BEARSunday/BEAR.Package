<?php

declare(strict_types=1);

namespace BEAR\Package\Module;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Ray\Di\AbstractModule;
use function class_exists;
use function interface_exists;

/**
 * @deprecated
 */
class CacheModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        if (interface_exists(Cache::class)) {
            assert(class_exists(ArrayCache::class)); // ensure doctrine/cache ^1 is installed.
            $this->bind(Cache::class)->to(ArrayCache::class);
        }
    }
}
