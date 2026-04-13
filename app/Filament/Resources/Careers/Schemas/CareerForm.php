<?php

namespace App\Filament\Resources\Careers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CareerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id')
                    ->image(),
                TextInput::make('salary'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('education'),
                TextInput::make('location'),
                TextInput::make('type')
                    ->required()
                    ->default('Full-time'),
                DatePicker::make('deadline'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('requirement')
                    ->columnSpanFull(),
                Textarea::make('benefit')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_home')
                    ->required(),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
