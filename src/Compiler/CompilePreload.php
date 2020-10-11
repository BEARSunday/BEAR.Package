<?php

declare(strict_types=1);

namespace BEAR\Package\Compiler;

use ArrayObject;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\AppMeta\Meta;

use function preg_quote;
use function preg_replace;
use function realpath;
use function sprintf;
use function strpos;

final class CompilePreload
{
    /** @var NewInstance */
    private $newInstance;

    /** @var CompileAutoload */
    private $dumpAutoload;

    /** @var ArrayObject<int, string> */
    private $classes;

    /** @var FilePutContents */
    private $filePutContents;

    /** @var string */
    private $context;

    /**
     * @param ArrayObject<int, string> $classes
     */
    public function __construct(NewInstance $newInstance, CompileAutoload $dumpAutoload, FilePutContents $filePutContents, ArrayObject $classes, string $context)
    {
        $this->newInstance = $newInstance;
        $this->dumpAutoload = $dumpAutoload;
        $this->classes = $classes;
        $this->filePutContents = $filePutContents;
        $this->context = $context;
    }

    public function __invoke(AbstractAppMeta $appMeta, string $context): string
    {
        $this->loadResources($appMeta->name, $context, $appMeta->appDir);
        /** @var list<string> $classes */
        $classes = (array) $this->classes;
        $paths = $this->dumpAutoload->getPaths($classes);
        $requiredOnceFile = '';
        foreach ($paths as $path) {
            $requiredOnceFile .= sprintf(
                "require_once %s';\n",
                $this->getRelativePath($appMeta->appDir, $path)
            );
        }

        $preloadFile = sprintf("<?php

// %s preload

require __DIR__ . '/vendor/autoload.php';

%s", $this->context, $requiredOnceFile);
        $fileName = realpath($appMeta->appDir) . '/preload.php';
        ($this->filePutContents)($fileName, $preloadFile);

        return $fileName;
    }

    public function loadResources(string $appName, string $context, string $appDir): void
    {
        $meta = new Meta($appName, $context, $appDir);

        $resMetas = $meta->getGenerator('*');
        foreach ($resMetas as $resMeta) {
            ($this->newInstance)($resMeta->class);
        }
    }

    private function getRelativePath(string $rootDir, string $path): string
    {
        $dir = (string) realpath($rootDir);
        if (strpos($path, $dir) !== false) {
            return (string) preg_replace('#^' . preg_quote($dir, '#') . '#', "__DIR__ . '", $path);
        }

        return $path;
    }
}