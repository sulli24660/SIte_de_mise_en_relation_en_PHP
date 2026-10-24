<?php

declare(strict_types=1);

namespace App\Validator;

class Validator
{
    public static function isRequired(string $value): bool
    {
        return $value !== '';
    }

    public static function hasMinLength(string $value, int $min): bool
    {
        return mb_strlen($value) >= $min;
    }

    public static function hasMaxLength(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    public static function hasMaxBytes(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }

    public static function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function matchesPattern(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }
}
