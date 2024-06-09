<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\EmbedInterceptor($singleton('BEAR\\Resource\\ResourceInterface-'));
$isSingleton = true;
return $instance;
