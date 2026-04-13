<?php

namespace App\Filament\Resources\AgencyRequests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AgencyRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('shop_name'),
                TextInput::make('address'),
                TextInput::make('area'),
                Textarea::make('details')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
            ]);
    }
}
