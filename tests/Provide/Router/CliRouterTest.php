<?php

namespace BEAR\Package;

use BEAR\Package\Provide\Router\CliRouter;
use BEAR\Sunday\Provide\Router\WebRouter;

class CliRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CliRouter
     */
    private $router;

    public function setUp()
    {
        $this->router = new CliRouter(new WebRouter('page://self'));
    }

    public function testMatch()
    {
        $globals = [
            'argv' => [
                'php',
                'get',
                'page://self/?name=bear'
            ],
            'argc' => 3
        ];
        $request = $this->router->match($globals, []);
        $this->assertSame('get', $request->method);
        $this->assertSame('page://self/', $request->path);
        $this->assertSame(['name' => 'bear'], $request->query);
    }
}
