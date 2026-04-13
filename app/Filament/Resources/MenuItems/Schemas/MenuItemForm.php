<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->default('custom'),
                TextInput::make('reference_id')
                    ->numeric(),
                TextInput::make('url')
                    ->url(),
                TextInput::make('parent_id')
                    ->numeric()
                    ->default(0),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
