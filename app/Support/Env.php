<?php
declare(strict_types=1);

namespace App\Support;

final class Env
{
    private static bool $loaded = false;

    public static function load(string $filePath): void
    {
        if (self::$loaded || !is_file($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            $parts = explode('=', $trimmed, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = self::normalizeValue(trim($parts[1]));

            if ($key === '') {
                continue;
            }

            self::set($key, $value);
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }

        return in_array(strtolower(trim($value)), ['1', 'true', 'on', 'yes'], true);
    }

    private static function normalizeValue(string $value): string
    {
        $length = strlen($value);
        if ($length >= 2) {
            $firstChar = $value[0];
            $lastChar = $value[$length - 1];

            $isSingleQuoted = $firstChar === "'" && $lastChar === "'";
            $isDoubleQuoted = $firstChar === '"' && $lastChar === '"';
            if ($isSingleQuoted || $isDoubleQuoted) {
                return substr($value, 1, -1);
            }
        }

        return $value;
    }

    private static function set(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv($key . '=' . $value);
    }
}
