<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('project_category_id')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_home')
                    ->required(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id'),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('gallery')->label('Thư viện ảnh')->multiple(),
                TextInput::make('banner'),
            ]);
    }
}
