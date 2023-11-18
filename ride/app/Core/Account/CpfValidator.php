<?php

declare(strict_types=1);

namespace App\Core\Account;

final class CpfValidator
{
    private function __construct()
    {
    }

    public static function validate(string $cpf): bool
    {
        if (!$cpf) return false;
        $cpf = self::cleanCpfInput($cpf);
        if (self::isInvalidLength($cpf)) return false;
        if (self::allDigitsAreTheSame($cpf)) return false;
        $dg1 = self::calculateDigit($cpf, 10);
        $dg2 = self::calculateDigit($cpf, 11);
        return self::extractCheckDigit($cpf) === $dg1 . $dg2;
    }

    private static function cleanCpfInput(string $cpf): string
    {
        return preg_replace('/[^0-9]/', "", $cpf);
    }

    private static function isInvalidLength(string $cpf): bool
    {
        return strlen($cpf) !== 11;
    }

    private static function allDigitsAreTheSame(string $cpf): bool
    {
        return count(array_unique(str_split($cpf))) === 1;
    }

    private static function calculateDigit(string $cpf, int $factor): int
    {
        $total = 0;
        foreach (str_split($cpf) as $digit) {
            if ($factor > 1) $total += $digit * $factor--;
        }
        $rest = $total % 11;
        return ($rest < 2) ? 0 : 11 - $rest;
    }

    private static function extractCheckDigit(string $cpf): string
    {
        return substr($cpf, -2);
    }
}
