<?php

declare(strict_types=1);

namespace BEAR\Package\Injector;

use BEAR\AppMeta\Meta;
use PHPUnit\Framework\TestCase;

use function dirname;
use function touch;
use function var_dump;

class FileUpdateTest extends TestCase
{
    public function testBindingUpdated(): void
    {
        $meta = new Meta('FakeVendor\HelloWorld', 'app');
        $bindingsUpdate = new FileUpdate($meta);

        $initialTime = $bindingsUpdate->getLatestUpdateTime($meta);
        var_dump("Initial time: " . $initialTime);

        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertTrue($isNotUpdated);

        // パスを正規化してtouchを実行
        $path = str_replace('/', DIRECTORY_SEPARATOR, dirname(__DIR__) . '/Fake/fake-app/src/Module/AppModule.php');
        touch($path);

        if (PHP_OS_FAMILY === 'Windows') {
            sleep(1);
        }

        $newTime = $bindingsUpdate->getLatestUpdateTime($meta);
        var_dump("New time: " . $newTime);

        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertFalse($isNotUpdated);
    }
}
