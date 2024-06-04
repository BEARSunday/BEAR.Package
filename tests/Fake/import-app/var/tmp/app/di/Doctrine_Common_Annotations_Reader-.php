<?php

namespace Ray\Di\Compiler;

$instance = new \Koriym\Attributes\DualReader($prototype('Doctrine\\Common\\Annotations\\Reader-annotation'), $prototype('Doctrine\\Common\\Annotations\\Reader-attribute'));
$isSingleton = false;
return $instance;
