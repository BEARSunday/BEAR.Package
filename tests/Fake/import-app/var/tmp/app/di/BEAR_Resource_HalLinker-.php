<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\HalLinker($prototype('BEAR\\Resource\\ReverseLinkerInterface-'));
$isSingleton = false;
return $instance;
