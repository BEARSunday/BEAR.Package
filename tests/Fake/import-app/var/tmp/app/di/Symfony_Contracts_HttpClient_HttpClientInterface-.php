<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\Module\HttpClientProvider();
$isSingleton = false;
return $instance->get();
