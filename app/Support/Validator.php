<?php
declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class Validator
{
    public static function requiredString(string $value, string $fieldName, int $maxLength = 255): string
    {
        $normalized = trim($value);
        if ($normalized === '') {
            throw new InvalidArgumentException($fieldName . ' is required.');
        }

        if (strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException($fieldName . ' exceeds max length of ' . $maxLength . '.');
        }

        return $normalized;
    }

    public static function optionalString(?string $value, string $fieldName, int $maxLength = 255): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        if (strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException($fieldName . ' exceeds max length of ' . $maxLength . '.');
        }

        return $normalized;
    }

    public static function integer(int|string $value, string $fieldName, int $min = 0, int $max = PHP_INT_MAX): int
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException($fieldName . ' must be numeric.');
        }

        $normalized = (int) $value;
        if ($normalized < $min || $normalized > $max) {
            throw new InvalidArgumentException($fieldName . ' must be between ' . $min . ' and ' . $max . '.');
        }

        return $normalized;
    }
}
