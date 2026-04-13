<?php

namespace App\Filament\Resources\ConsultingRequests\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConsultingRequestForm
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
                TextInput::make('company'),
                TextInput::make('address'),
                Textarea::make('details')
                    ->columnSpanFull(),
                TextInput::make('file_path'),
                TextInput::make('budget'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
            ]);
    }
}
