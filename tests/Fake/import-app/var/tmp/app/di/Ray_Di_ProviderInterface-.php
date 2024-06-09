<?php

namespace Ray\Di\Compiler;

$instance = new \Ray\Di\ProviderSetProvider($injectionPoint(), $injector(), $prototype('Koriym\\ParamReader\\ParamReaderInterface-'));
$isSingleton = false;
return $instance->get();
