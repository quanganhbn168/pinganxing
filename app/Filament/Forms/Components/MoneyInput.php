<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class MoneyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->suffix('₫')
            ->placeholder('0')
            ->inputMode('numeric')
            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
            ->minValue(0)
            ->dehydrateStateUsing(fn ($state) => static::normalizeMoney($state))
            ->formatStateUsing(fn ($state) => static::formatMoney($state));
    }

    public function zeroWhenEmpty(): static
    {
        $this->dehydrateStateUsing(
            fn ($state) => static::normalizeMoney($state) ?? 0
        );

        return $this;
    }

    protected static function normalizeMoney($state): ?int
    {
        if (! filled($state)) {
            return null;
        }

        $state = trim((string) $state);

        // Nếu từ DB ra dạng decimal: 2180000.00
        if (preg_match('/^\d+\.\d{2}$/', $state)) {
            return (int) floor((float) $state);
        }

        // Nếu từ form nhập dạng VN: 2.180.000
        $state = str_replace(['.', ',', '₫', ' '], '', $state);

        return (int) $state;
    }

    protected static function formatMoney($state): ?string
    {
        if (! filled($state)) {
            return null;
        }

        return number_format((float) $state, 0, ',', '.');
    }
}