<?php

namespace Ray\Di\Compiler;

$instance = new \Import\HelloWorld\Resource\Page\Index();
$instance->setRenderer($singleton('BEAR\\Resource\\RenderInterface-'));
$isSingleton = false;
return $instance;
