<?php

namespace App\Support;

class PasswordPolicy
{
    /**
     * Returns the regex pattern for validating passwords.
     *
     * The pattern requires at least 8 characters, and must contain at least one lowercase letter, one uppercase letter, and one digit.
     * @return string The regex pattern for validating passwords.
     */
    public static function regex(): string
    {
        return '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    }

    public static function pass(string $password): bool
    {
        return preg_match(self::regex(), $password) === 1;
    }
}