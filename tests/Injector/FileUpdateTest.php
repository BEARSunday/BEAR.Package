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
        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertTrue($isNotUpdated);
        // touch normalized path
        $normalizedPath = str_replace('\\', '/', dirname(__DIR__) . '/Fake/fake-app/src/Module/AppModule.php');
        touch($normalizedPath);
        $isNotUpdated = $bindingsUpdate->isNotUpdated($meta);
        $this->assertFalse($isNotUpdated);
    }
}
