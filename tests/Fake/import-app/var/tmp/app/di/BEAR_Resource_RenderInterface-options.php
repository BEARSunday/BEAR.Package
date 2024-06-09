<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\OptionsRenderer($prototype('BEAR\\Resource\\OptionsMethods-'), true);
$isSingleton = false;
return $instance;
