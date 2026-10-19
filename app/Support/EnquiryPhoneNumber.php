<?php

namespace App\Support;

class EnquiryPhoneNumber
{
    public const MESSAGE = 'Enter a valid Indian mobile number (10 digits, starting with 6–9), or an international number with + and its country code.';

    public static function normalize(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = trim($value);
        if ($value === '' || strlen($value) > 24 || ! preg_match('/^\+?[0-9() -]+$/D', $value)) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $value);
        $international = str_starts_with($value, '+') || str_starts_with($digits, '00');
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (! $international && strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        if (! $international && strlen($digits) === 10) {
            $digits = '91'.$digits;
        } elseif (! $international && ! (strlen($digits) === 12 && str_starts_with($digits, '91'))) {
            return null;
        }
        if (strlen($digits) < 8 || strlen($digits) > 15 || $digits[0] === '0' || preg_match('/^(\d)\1+$/D', $digits)) {
            return null;
        }
        if (str_starts_with($digits, '91')) {
            $national = substr($digits, 2);
            if (! preg_match('/^[6-9]\d{9}$/D', $national) || preg_match('/^(\d)\1+$/D', $national)) {
                return null;
            }
        }

        return '+'.$digits;
    }
}
