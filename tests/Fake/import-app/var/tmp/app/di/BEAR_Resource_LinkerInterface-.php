<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\Linker($prototype('BEAR\\Resource\\InvokerInterface-'), $prototype('BEAR\\Resource\\FactoryInterface-'));
$isSingleton = false;
return $instance;
