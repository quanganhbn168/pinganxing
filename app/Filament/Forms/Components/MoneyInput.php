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
            ->prefix('₫')
            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
            ->stripCharacters('.')
            ->numeric()
            ->minValue(0)
            ->dehydrateStateUsing(
                fn ($state) => filled($state)
                    ? (int) str_replace('.', '', $state)
                    : null
            )
            ->formatStateUsing(
                fn ($state) => filled($state)
                    ? number_format((int) $state, 0, ',', '.')
                    : null
            );
    }

    public function zeroWhenEmpty(): static
    {
        $this->dehydrateStateUsing(
            fn ($state) => filled($state)
                ? (int) str_replace('.', '', $state)
                : 0
        );

        return $this;
    }
}