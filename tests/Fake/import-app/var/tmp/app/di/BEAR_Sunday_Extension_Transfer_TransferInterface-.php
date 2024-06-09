<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Sunday\Provide\Transfer\HttpResponder($prototype('BEAR\\Sunday\\Provide\\Transfer\\HeaderInterface-'), $prototype('BEAR\\Sunday\\Provide\\Transfer\\ConditionalResponseInterface-'));
$isSingleton = false;
return $instance;
