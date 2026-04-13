<?php

namespace App\Filament\Resources\Intros\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class IntroForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->columnSpanFull(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id')
                    ->image(),
                TextInput::make('banner'),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_home')
                    ->required(),
                Toggle::make('is_main')
                    ->required(),
            ]);
    }
}
