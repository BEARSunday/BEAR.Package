<?php

namespace Ray\Di\Compiler;

$instance = new \Koriym\ParamReader\ParamReader($prototype('Doctrine\\Common\\Annotations\\Reader-'));
$isSingleton = false;
return $instance;
