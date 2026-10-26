<?php

namespace Tests\Unit;

use App\Services\WhatsApp;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class WhatsAppTest extends TestCase
{
    #[TestWith(['081234567890', '6281234567890'])]
    #[TestWith(['0812-3456-7890', '6281234567890'])]
    #[TestWith(['+62 812 3456 7890', '6281234567890'])]
    #[TestWith(['81234567890', '6281234567890'])]
    public function test_normalizes_indonesian_numbers_to_international_format(string $input, string $expected): void
    {
        $this->assertSame($expected, WhatsApp::normalize($input));
    }

    #[TestWith(['081234567890', true])]
    #[TestWith(['12345', false])]
    #[TestWith(['0211234567', false])]
    #[TestWith(['', false])]
    public function test_validates_mobile_numbers(string $input, bool $expected): void
    {
        $this->assertSame($expected, WhatsApp::isValid($input));
    }

    public function test_link_uses_https_and_url_encodes_message(): void
    {
        $this->assertSame(
            'https://wa.me/6281234567890?text=Halo%20%26%20salam%0Abaris',
            WhatsApp::link('081234567890', "Halo & salam\nbaris"),
        );
    }
}
