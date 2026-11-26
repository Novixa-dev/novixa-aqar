<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    public function test_whole_amounts_are_shown_without_trailing_decimals(): void
    {
        $currency = app(CurrencyService::class);

        $this->assertSame('$285,000', $currency->format(285000, 'USD'));
        $this->assertSame('450,000 ر.ي', $currency->format(450000, 'YER'));
        $this->assertSame('1,200 ر.س', $currency->format(1200, 'SAR'));
    }

    public function test_fractional_amounts_keep_the_currency_precision(): void
    {
        $currency = app(CurrencyService::class);

        $this->assertSame('$450.50', $currency->format(450.5, 'USD'));
        $this->assertSame('99.95 ر.س', $currency->format(99.95, 'SAR'));
    }

    public function test_unknown_codes_fall_back_to_the_code_as_symbol(): void
    {
        $this->assertSame('GBP1,000', app(CurrencyService::class)->format(1000, 'GBP'));
    }
}
