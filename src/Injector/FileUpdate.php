<?php

declare(strict_types=1);

namespace BEAR\Package\Injector;

use BEAR\AppMeta\AbstractAppMeta;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;

use function array_map;
use function assert;
use function file_exists;
use function filemtime;
use function glob;
use function max;
use function preg_quote;
use function rtrim;
use function sprintf;
use function str_replace;

use const DIRECTORY_SEPARATOR;

final class FileUpdate
{
    private int $updateTime;
    private string $srcRegex;
    private string $varRegex;

    public function __construct(AbstractAppMeta $meta)
    {
        $normalizedAppDir = str_replace('\\', '/', rtrim($meta->appDir, '\\/')) . '/';
        $this->srcRegex = sprintf('#^(?!.*(%ssrc/Resource)).*?$#m', $normalizedAppDir);
        $this->varRegex = sprintf(
            '#^(?!%s(?:var/tmp|var/log|var/templates|var/phinx)).*$#',
            preg_quote($normalizedAppDir, '#'),
        );
        $this->updateTime = $this->getLatestUpdateTime($meta);
    }

    public function isNotUpdated(AbstractAppMeta $meta): bool
    {
        return $this->getLatestUpdateTime($meta) === $this->updateTime;
    }

    public function getLatestUpdateTime(AbstractAppMeta $meta): int
    {
        $srcFiles = $this->getFiles($meta->appDir . DIRECTORY_SEPARATOR . 'src', $this->srcRegex);
        $varFiles = $this->getFiles($meta->appDir . DIRECTORY_SEPARATOR . 'var', $this->varRegex);
        $envFiles = (array) glob($meta->appDir . DIRECTORY_SEPARATOR . '.env*');
        $scanFiles = [...$srcFiles, ...$varFiles, ...$envFiles];
        $composerLock = $meta->appDir . DIRECTORY_SEPARATOR . 'composer.lock';
        if (file_exists($composerLock)) {
            $scanFiles[] = $composerLock;
        }

        /** @psalm-suppress all -- ignore filemtime could return false */
        return (int) max(array_map([$this, 'filemtime'], $scanFiles));
    }

    /** @SuppressWarnings(PHPMD.UnusedPrivateMethod) */
    private function filemtime(string $filename): string
    {
        return (string) filemtime($filename);
    }

    /** @return list<string> */
    private function getFiles(string $path, string $regex): array
    {
        // 正規表現用にパスを正規化
        $normalizedPath = str_replace('\\', '/', $path);

        // DirectoryIterator用にはWindowsネイティブパスを使用
        $iteratorPath = str_replace('/', DIRECTORY_SEPARATOR, $path);

        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $iteratorPath,
                    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::SKIP_DOTS,
                ),
                RecursiveIteratorIterator::LEAVES_ONLY,
            ),
            $regex,
            RecursiveRegexIterator::MATCH,
        );

        $files = [];
        foreach ($iterator as $fileName => $fileInfo) {
            $normalizedFileName = str_replace('\\', '/', $fileName);
            assert($fileInfo instanceof SplFileInfo);
            if (! $fileInfo->isFile() || $fileInfo->getFilename()[0] === '.') {
                continue;
            }

            $files[] = $normalizedFileName;
        }

        return $files;
    }
}
