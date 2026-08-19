<?php

namespace App\Services;

class CurrencyService
{
    /**
     * All currencies known to the app (supported + legacy), keyed by ISO code.
     */
    public function all(): array
    {
        return array_merge(
            config('currencies.supported', []),
            config('currencies.legacy', [])
        );
    }

    /**
     * Currencies a user may actually select (for form/filter options).
     */
    public function supported(): array
    {
        return config('currencies.supported', []);
    }

    public function isSupported(?string $code): bool
    {
        return $code && array_key_exists(strtoupper($code), $this->supported());
    }

    public function default(): string
    {
        return config('currencies.default', 'YER');
    }

    /**
     * Options for a Filament/HTML select: ['YER' => 'ريال يمني (YER)', ...].
     */
    public function options(bool $arabic = false): array
    {
        $options = [];
        foreach ($this->supported() as $code => $currency) {
            $name = $arabic ? ($currency['name_ar'] ?? $currency['name']) : $currency['name'];
            $options[$code] = "{$name} ({$code})";
        }

        return $options;
    }

    public function symbol(?string $code): string
    {
        $code = strtoupper((string) $code);

        return $this->all()[$code]['symbol'] ?? $code;
    }

    public function name(?string $code, bool $arabic = false): string
    {
        $code = strtoupper((string) $code);
        $currency = $this->all()[$code] ?? null;

        if (! $currency) {
            return $code;
        }

        return $arabic ? ($currency['name_ar'] ?? $currency['name']) : $currency['name'];
    }

    /**
     * Format a numeric amount using the given currency's own decimal
     * precision, symbol, and symbol position. Falls back to the app's
     * default currency if none is given, and to the raw code as a
     * "symbol" if the currency isn't in config/currencies.php at all
     * (e.g. a legacy or unexpected value already stored on a record).
     */
    public function format(?float $amount, ?string $code = null): string
    {
        $code = strtoupper($code ?: $this->default());
        $currency = $this->all()[$code] ?? [
            'symbol' => $code,
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_separator' => ',',
        ];

        $number = number_format(
            (float) $amount,
            $currency['decimals'],
            '.',
            $currency['thousands_separator']
        );

        return $currency['symbol_position'] === 'before'
            ? "{$currency['symbol']}{$number}"
            : "{$number} {$currency['symbol']}";
    }
}
