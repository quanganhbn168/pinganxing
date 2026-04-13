<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('position'),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id')
                    ->image(),
                TextInput::make('level'),
                TextInput::make('experience')
                    ->numeric(),
                Textarea::make('bio')
                    ->columnSpanFull(),
            ]);
    }
}
