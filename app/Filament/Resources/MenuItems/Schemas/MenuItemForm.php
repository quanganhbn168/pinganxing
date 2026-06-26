<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin menu item')
                    ->schema([
                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required(),

                        TextInput::make('type')
                            ->label('Loại liên kết')
                            ->required()
                            ->default('custom'),

                        TextInput::make('reference_id')
                            ->label('ID liên kết')
                            ->numeric(),

                        TextInput::make('url')
                            ->label('URL')
                            ->columnSpanFull(),

                        TextInput::make('parent_id')
                            ->label('ID cha')
                            ->numeric()
                            ->default(0),

                        TextInput::make('position')
                            ->label('Thứ tự')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
