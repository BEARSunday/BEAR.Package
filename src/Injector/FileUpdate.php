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
use function var_dump;

use const DIRECTORY_SEPARATOR;

final class FileUpdate
{
    private int $updateTime;
    private string $srcRegex;
    private string $varRegex;

    public function __construct(AbstractAppMeta $meta)
    {
        $normalizedAppDir = str_replace(['\\', ':'], ['/', ''], rtrim($meta->appDir, '\\/')) . '/';
        $this->srcRegex = sprintf('#^(?!.*(%ssrc/Resource)).*?$#m', $normalizedAppDir);
        $this->varRegex = sprintf(
            '#^(?!.*(%s|%s|%s|%s)).*$#',
            preg_quote($normalizedAppDir . 'var/tmp', '#'),
            preg_quote($normalizedAppDir . 'var/log', '#'),
            preg_quote($normalizedAppDir . 'var/templates', '#'),
            preg_quote($normalizedAppDir . 'var/phinx', '#'),
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

    /** @return list<RecursiveDirectoryIterator> */
    private function getFiles(string $path, string $regex): array
    {
        $iterator = new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $path,
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
            var_dump($normalizedFileName);
            assert($fileInfo instanceof SplFileInfo);
            if (! $fileInfo->isFile() || $fileInfo->getFilename()[0] === '.') {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $files[] = $normalizedFileName;
        }

        return $files; // @phpstan-ignore-line
    }
}
