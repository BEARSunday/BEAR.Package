<?php

namespace BEAR\Package\Provide\Application;

use Any\Serializer\Serializer;
use Aura\Router\DefinitionFactory;
use Aura\Router\Map;
use Aura\Router\RouteFactory;
use Aura\Signal\HandlerFactory;
use Aura\Signal\Manager;
use Aura\Signal\ResultCollection;
use Aura\Signal\ResultFactory;
use BEAR\Package\Dev\Debug\ExceptionHandle\ErrorPage;
use BEAR\Package\Dev\Debug\ExceptionHandle\ExceptionHandler;
use BEAR\Package\Provide\ApplicationLogger\ApplicationLogger;
use BEAR\Package\Provide\ConsoleOutput\ConsoleOutput;
use BEAR\Package\Provide\Router\AuraRouter;
use BEAR\Package\Provide\WebResponse\HttpFoundation as WebResponse;
use BEAR\Resource\Anchor;
use BEAR\Resource\Factory;
use BEAR\Resource\Invoker;
use BEAR\Resource\Linker;

use BEAR\Resource\Logger;
use BEAR\Resource\NamedParameter;
use BEAR\Resource\Param;
use BEAR\Resource\Request;
use BEAR\Resource\Resource;
use BEAR\Resource\SchemeCollection;
use BEAR\Resource\SignalParameter;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Guzzle\Parser\UriTemplate\UriTemplate;
use PHPParser_BuilderFactory;
use PHPParser_Lexer;
use PHPParser_Parser;
use PHPParser_PrettyPrinter_Default;
use Ray\Aop\Bind;
use Ray\Aop\Compiler;
use Ray\Di\Annotation;
use Ray\Di\Config;
use Ray\Di\Container;
use Ray\Di\Definition;
use Ray\Di\EmptyModule;
use Ray\Di\Forge;
use Ray\Di\Injector;

use Ray\Di\Logger as DiLogger;

class MyApp extends AbstractApp
{
}

class AbstractApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MyApp
     */
    protected $app;

    protected function setUp()
    {
        $invoker = new Invoker(
            new Linker(
                new AnnotationReader,
                new ArrayCache,
                new UriTemplate
            ),
            new NamedParameter(
                new SignalParameter(
                    new Manager(
                        new HandlerFactory,
                        new ResultFactory,
                        new ResultCollection
                    ),
                    new Param
                )
            ),
            new Logger
        );
        $this->app = new MyApp(
            new Injector(
                new Container(
                    new Forge(new Config(new Annotation(new Definition, new AnnotationReader)))
                ),
                new EmptyModule,
                new Bind,
                new Compiler(
                    sys_get_temp_dir(),
                    new PHPParser_PrettyPrinter_Default,
                    new PHPParser_Parser(new PHPParser_Lexer),
                    new PHPParser_BuilderFactory
                ),
                new DiLogger
            ),
            new Resource(
                new Factory(new SchemeCollection),
                $invoker,
                new Request($invoker),
                new Anchor(
                    new UriTemplate,
                    new AnnotationReader,
                    new Request($invoker)
                )
            ),
            new ExceptionHandler(
                new WebResponse(new ConsoleOutput),
                'BEAR/Package/Module/ExceptionHandle/template/view.php',
                new ErrorPage
            ),
            new ApplicationLogger(
                new Logger(new Serializer)
            ),
            new WebResponse(
                new ConsoleOutput
            ),
            new AuraRouter(
                new Map(new DefinitionFactory, new RouteFactory)
            )
        );
    }

    public function testNew()
    {
        $this->assertInstanceOf('BEAR\Package\Provide\Application\AbstractApp', $this->app);
    }
}
