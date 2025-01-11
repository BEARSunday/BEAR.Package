<?php

declare(strict_types=1);

namespace BEAR\Package\Injector;

use BEAR\AppMeta\Meta;
use PHPUnit\Framework\TestCase;

use function dirname;
use function error_log;
use function str_replace;
use function time;
use function touch;

use const DIRECTORY_SEPARATOR;

class FileUpdateTest extends TestCase
{
    public function testBindingUpdated(): void
    {
        $meta = new Meta('FakeVendor\HelloWorld', 'app');
        $bindingsUpdate = new FileUpdate($meta);

        $initialTime = $bindingsUpdate->getLatestUpdateTime($meta);
        error_log('Initial time: ' . $initialTime);

        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertTrue($isNotUpdated);

        $path = str_replace('/', DIRECTORY_SEPARATOR, dirname(__DIR__) . '/Fake/fake-app/src/Module/AppModule.php');
        touch($path, time() + 1);

        $newTime = $bindingsUpdate->getLatestUpdateTime($meta);
        error_log('New time: ' . $newTime);

        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertFalse($isNotUpdated);
    }
}
