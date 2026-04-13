<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Enums\TagType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Select::make('type')
                    ->options(TagType::class)
                    ->default('product')
                    ->required(),
                TextInput::make('color')
                    ->required()
                    ->default('#6c757d'),
                TextInput::make('description'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
