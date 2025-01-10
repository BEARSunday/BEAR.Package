<?php

declare(strict_types=1);

namespace BEAR\Package;

use PHPUnit\Framework\Assert;

use function array_keys;
use function count;
use function is_array;
use function is_string;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function range;
use function sort;
use function str_replace;

use const JSON_ERROR_NONE;

trait AssertJsonTrait
{
    /**
     * Custom assertion for platform-independent JSON comparison
     * Handles differences in line endings (CRLF vs LF) and formatting
     *
     * @param string|array $expected Expected value (JSON string or array)
     * @param string|array $actual   Actual value (JSON string or array)
     * @param string       $message  Optional failure message
     */

    /**
     * @param array<mixed>|string|null $expected
     * @param array<mixed>|string|null $actual
     */
    public static function assertSameJson(string|array|null $expected, string|array|null $actual, string $message = ''): void
    {
        // Normalize line endings and decode if strings
        if (is_string($expected)) {
            $expected = str_replace(["\r\n", "\r"], "\n", $expected);
            $expected = json_decode($expected, true);
        }

        if (is_string($actual)) {
            $actual = str_replace(["\r\n", "\r"], "\n", $actual);
            $actual = json_decode($actual, true);
        }

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            Assert::fail($message ?: 'Invalid JSON: ' . json_last_error_msg());
        }

        // Normalize and compare using assertEquals instead of assertSame
        $normalizedExpected = self::normalizeJson($expected);
        $normalizedActual = self::normalizeJson($actual);

        Assert::assertEquals(
            $normalizedExpected,
            $normalizedActual,
            $message ?: 'JSON values are not equal',
        );
    }

    /**
     * @param non-empty-string       $expected
     * @param non-empty-string|false $actual
     */
    public function assertNormalizedStringEquals(string $expected, string|false $actual, string $message = ''): void
    {
        Assert::assertEquals(
            self::normalizeString($expected),
            self::normalizeString($actual),
            $message,
        );
    }

    /**
     * Normalizes JSON arrays by sorting numeric arrays and recursively processing nested structures
     *
     * @param array $data Array to normalize
     *
     * @return array Normalized array
     */

    /**
     * @param array<mixed>|mixed $data
     *
     * @return array<mixed>
     */
    private static function normalizeJson(mixed $data): array
    {
        if (! is_array($data)) {
            return (array) $data;
        }

        $normalized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $normalized[$key] = self::normalizeJson($value);
            } else {
                $normalized[$key] = $value;
            }
        }

        // Sort if numeric array
        if (array_keys($normalized) === range(0, count($normalized) - 1)) {
            sort($normalized);
        }

        return $normalized;
    }

    /**
     * Normalize line endings for both HTTP and JSON responses
     */
    private static function normalizeString(string|false $string): string
    {
        if ($string === false) {
            return '';
        }

        return str_replace(["\r\n", "\r"], "\n", $string);
    }
}
