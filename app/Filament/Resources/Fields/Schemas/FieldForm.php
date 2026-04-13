<?php

namespace App\Filament\Resources\Fields\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FieldForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('field_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id')
                    ->image(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('gallery')->label('Thư viện ảnh')->multiple(),
                Textarea::make('summary')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
                TextInput::make('meta_keywords'),
            ]);
    }
}
