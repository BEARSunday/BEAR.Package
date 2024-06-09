<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\Invoker($prototype('BEAR\\Resource\\PhpClassInvoker-'));
$isSingleton = false;
return $instance;
