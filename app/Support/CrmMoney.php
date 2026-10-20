<?php

namespace App\Support;

use InvalidArgumentException;

class CrmMoney
{
    public const CURRENCIES = ['INR', 'USD', 'GBP', 'EUR', 'AED', 'AUD', 'CAD', 'NZD'];

    public static function minor(string $amount): int
    {
        if (! preg_match('/^(0|[1-9][0-9]{0,8})(?:\.([0-9]{1,2}))?$/D', $amount, $matches)) {
            throw new InvalidArgumentException('Use an amount with up to two decimal places.');
        }

        return ((int) $matches[1] * 100) + (int) str_pad($matches[2] ?? '', 2, '0');
    }

    public static function display(int $minor): string
    {
        $absolute = abs($minor);

        return ($minor < 0 ? '-' : '').intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function tax(int $base, int $basisPoints): int
    {
        return intdiv($base * $basisPoints + 5000, 10000);
    }
}
