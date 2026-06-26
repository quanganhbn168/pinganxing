<?php

namespace App\Filament\Resources\Newsletters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đăng ký nhận tin')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email đăng ký')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
