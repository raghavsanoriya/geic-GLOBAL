<?php

namespace App\Rules;

use App\Support\EnquiryPhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidEnquiryPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (EnquiryPhoneNumber::normalize($value) === null) {
            $fail(EnquiryPhoneNumber::MESSAGE);
        }
    }
}
