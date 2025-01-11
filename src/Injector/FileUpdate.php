<?php

declare(strict_types=1);

namespace BEAR\Package\Injector;

use BEAR\AppMeta\AbstractAppMeta;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function array_map;
use function file_exists;
use function filemtime;
use function glob;
use function max;
use function preg_match;
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
            '#^(?!%s(?:var%stmp|var%slog|var%stemplates|var%sphinx)).*$#',
            preg_quote($normalizedAppDir, '#'),
            preg_quote(DIRECTORY_SEPARATOR, '#'),
            preg_quote(DIRECTORY_SEPARATOR, '#'),
            preg_quote(DIRECTORY_SEPARATOR, '#'),
            preg_quote(DIRECTORY_SEPARATOR, '#'),
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
        $iteratorPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $rdi = new RecursiveDirectoryIterator(
            $iteratorPath,
            FilesystemIterator::CURRENT_AS_FILEINFO
            | FilesystemIterator::KEY_AS_PATHNAME
            | FilesystemIterator::SKIP_DOTS,
        );
        $rdiIterator = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::LEAVES_ONLY);

        $files = [];
        foreach ($rdiIterator as $key => $fileInfo) {
            $normalizedFileName = str_replace('\\', '/', $key);
            if (! preg_match($regex, $normalizedFileName)) {
                continue;
            }

            if (! $fileInfo->isFile() || $fileInfo->getFilename()[0] === '.') {
                continue;
            }

            $files[] = $normalizedFileName;
        }

        return $files;
    }
}
