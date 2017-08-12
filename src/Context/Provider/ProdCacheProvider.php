<?php
/**
 * This file is part of the BEAR.Package package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Context\Provider;

use BEAR\AppMeta\AbstractAppMeta;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Cache\FilesystemCache;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

class ProdCacheProvider implements ProviderInterface
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @Named("namespace=cache_namespace")
     */
    public function __construct(AbstractAppMeta $appMeta, $namespace)
    {
        $this->cacheDir = $appMeta->tmpDir . '/cache';
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $cache = new ChainCache([new ApcuCache, new FilesystemCache($this->cacheDir)]);
        $cache->setNamespace($this->namespace);

        return $cache;
    }
}