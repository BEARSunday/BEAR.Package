<?php

namespace Ray\Di\Compiler;

$instance = new \Ray\Di\AssistedInjectInterceptor($injector(), $singleton('Ray\\Di\\MethodInvocationProvider-'));
$isSingleton = true;
return $instance;
