<?php
namespace BEAR\Package\Provide\Error;

use BEAR\Resource\Exception\BadRequestException;
use BEAR\Resource\Exception\ResourceNotFoundException;
use BEAR\Sunday\Extension\Router\RouterMatch;

class DevStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouterMatch
     */
    private $request;

    public function setUp()
    {
        parent::setUp();
        $request = new RouterMatch();
        list($request->method, $request->path, $request->query) = ['get', '/', []];
        $this->request = $request;
    }

    public function testRuntimeException()
    {
        $e = new \RuntimeException();
        $status =  new Status($e);
        $this->assertSame(503, $status->code);
    }

    public function testBadRequest()
    {
        $e = new BadRequestException;
        $status =  new Status($e);
        $this->assertSame(400, $status->code);
    }

    public function testNotFound()
    {
        $e = new ResourceNotFoundException('/__not_found__');
        $status =  new Status($e);
        $this->assertSame(404, $status->code);
    }
}
