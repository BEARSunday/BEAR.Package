<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\PhpClassInvoker($prototype('BEAR\\Resource\\NamedParameterInterface-'), $prototype('BEAR\\Resource\\ExtraMethodInvoker-'), $prototype('BEAR\\Resource\\LoggerInterface-'));
$isSingleton = false;
return $instance;
