<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\Module\SchemeCollectionProvider('Import\\HelloWorld', $injector());
$isSingleton = false;
return $instance->get();
