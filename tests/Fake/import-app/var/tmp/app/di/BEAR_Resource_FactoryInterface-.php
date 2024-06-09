<?php

namespace Ray\Di\Compiler;

$instance = new \BEAR\Resource\Factory($prototype('BEAR\\Resource\\SchemeCollectionInterface-', array('BEAR\\Resource\\Factory', '__construct', 'scheme')), $prototype('BEAR\\Resource\\UriFactory-'));
$instance->setSchemeCollection($prototype('BEAR\\Resource\\SchemeCollectionInterface-', array('BEAR\\Resource\\Factory', 'setSchemeCollection', 'scheme')));
$isSingleton = false;
return $instance;
