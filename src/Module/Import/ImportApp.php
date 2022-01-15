<?php

declare(strict_types=1);

namespace BEAR\Package\Module\Import;

use ReflectionClass;

use function assert;
use function class_exists;
use function dirname;
use function is_dir;
use function sprintf;

final class ImportApp
{
    public string $host;
    public string $appName;
    public string $context;
    public string $appDir;

    public function __construct(string $host, string $appName, string $context)
    {
        $this->host = $host;
        $this->appName = $appName;
        $this->context = $context;
        /** @var class-string $appModuleClass */
        $appModuleClass = sprintf('%s\\Module\\AppModule', $this->appName);
        $appModuleClassName = (string) (new ReflectionClass($appModuleClass))->getFileName();
        $appDir = dirname($appModuleClassName, 3);
        assert(is_dir($appDir));
        $this->appDir = $appDir;
    }
}
