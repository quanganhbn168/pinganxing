<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('address'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('subject'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
            ]);
    }
}
