<?php

namespace Tests\Feature;

use App\Rules\ValidEnquiryPhone;
use App\Support\EnquiryPhoneNumber;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidEnquiryPhoneTest extends TestCase
{
    #[DataProvider('validPhones')]
    public function test_accepts_supported_formats_and_normalizes_matching_numbers(string $input, string $expected): void
    {
        $validator = Validator::make(['phone' => $input], ['phone' => [new ValidEnquiryPhone]]);

        $this->assertTrue($validator->passes());
        $this->assertSame($expected, EnquiryPhoneNumber::normalize($input));
    }

    public static function validPhones(): array
    {
        return [
            'Indian mobile' => ['9876543210', '+919876543210'],
            'spaced mobile' => ['98765 43210', '+919876543210'],
            'country code' => ['+91 (98765) 43210', '+919876543210'],
            'unprefixed Indian code' => ['919876543210', '+919876543210'],
            'Indian trunk prefix' => ['09876543210', '+919876543210'],
            'international dial prefix' => ['00919876543210', '+919876543210'],
            'United States' => ['+1 (202) 555-0101', '+12025550101'],
            'United Kingdom' => ['+44 7700 900123', '+447700900123'],
            'Dubai' => ['+971 50 123 4567', '+971501234567'],
        ];
    }

    #[DataProvider('invalidPhones')]
    public function test_rejects_malformed_numbers_with_a_clear_message(mixed $input): void
    {
        $validator = Validator::make(['phone' => $input], ['phone' => [new ValidEnquiryPhone]]);

        $this->assertFalse($validator->passes());
        $this->assertSame([EnquiryPhoneNumber::MESSAGE], $validator->errors()->get('phone'));
    }

    public static function invalidPhones(): array
    {
        return [
            'email in phone' => ['someone@example.com'],
            'symbols without digits' => ['--------'],
            'all zeros' => ['0000000000'],
            'repeated digits' => ['9999999999'],
            'invalid Indian prefix' => ['1234567890'],
            'too short' => ['9876543'],
            'too long' => ['+1234567890123456'],
            'plus in middle' => ['98765+43210'],
            'multiple country markers' => ['++919876543210'],
            'letters' => ['9876543210 ext 4'],
            'embedded newline' => ["98765\n43210"],
            'array input' => [['9876543210']],
            'invalid India international' => ['+91 1234567890'],
        ];
    }
}
