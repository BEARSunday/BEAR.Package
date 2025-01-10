<?php

declare(strict_types=1);

namespace BEAR\Package\Provide\Error;

use BEAR\Sunday\Extension\Router\RouterMatch;
use LogicException;
use PHPUnit\Framework\TestCase;

use function json_decode;

class DevVndErrorPageTest extends TestCase
{
    private DevVndErrorPage $page;

    protected function setUp(): void
    {
        parent::setUp();

        $e = new LogicException('bear');
        $request = new RouterMatch();
        [$request->method, $request->path, $request->query] = ['get', '/', []];
        $this->page = (new DevVndErrorPageFactory())->newInstance($e, $request);
    }

    public function testToString(): void
    {
        $actual = (string) $this->page;

        $expected = '{
        "message": "Internal Server Error",
        "logref": "{logref}",
        "request": "get /",
        "exceptions": "LogicException(bear)"
    }';

        $actualArray = json_decode($actual, true);
        $expectedArray = json_decode($expected, true);

        $this->assertSame($expectedArray['message'], $actualArray['message']);
        $this->assertSame($expectedArray['logref'], $actualArray['logref']);
        $this->assertSame($expectedArray['request'], $actualArray['request']);
        $this->assertSame($expectedArray['exceptions'], $actualArray['exceptions']);

        $this->assertArrayHasKey('file', $actualArray);
    }
}
