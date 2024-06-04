<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Package\Provide\Error\NullPage();
$instance->setRenderer($singleton('BEAR\\Resource\\RenderInterface-'));
$isSingleton = false;
return $instance;
