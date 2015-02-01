<?php

namespace MyVendor\MyApp\Module;

use BEAR\Package\AppMeta;
use BEAR\Package\PackageModule;
use BEAR\Package\Provide\Router\AuraRouterModule;
use Ray\Di\AbstractModule;

class AppModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->install(new PackageModule(new AppMeta('MyVendor\MyApp')));
        $this->override(new AuraRouterModule);
    }
}
