<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('post_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('image_id')
                    ->image()
                    ->required(),
                \Awcodes\Curator\Components\Forms\CuratorPicker::make('gallery')->label('Thư viện ảnh')->multiple(),
                TextInput::make('banner'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_home')
                    ->required(),
                Toggle::make('is_menu')
                    ->required(),
                Toggle::make('is_footer')
                    ->required(),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                FileUpload::make('meta_image')
                    ->image(),
            ]);
    }
}
