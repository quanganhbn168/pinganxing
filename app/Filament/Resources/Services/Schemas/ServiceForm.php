<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('service_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
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
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->required(),
                Toggle::make('is_home')
                    ->required(),
                Toggle::make('is_menu')
                    ->required(),
                Toggle::make('is_footer')
                    ->required(),
                TextInput::make('unit_id')
                    ->numeric(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                FileUpload::make('meta_image')
                    ->image(),
                \Filament\Forms\Components\Section::make('Liên kết Landing Page')
                    ->description('Chọn các thực thể liên quan để hiển thị ở đáy trang (dạng Mini-landing page).')
                    ->schema([
                        \Filament\Forms\Components\Select::make('projects')
                            ->label('Dự án đã triển khai')
                            ->multiple()
                            ->relationship('projects', 'name')
                            ->preload(),
                        \Filament\Forms\Components\Select::make('products')
                            ->label('Sản phẩm / Phân hệ')
                            ->multiple()
                            ->relationship('products', 'name')
                            ->preload(),
                        \Filament\Forms\Components\Select::make('posts')
                            ->label('Bài viết tham khảo')
                            ->multiple()
                            ->relationship('posts', 'title')
                            ->preload(),
                    ]),
            ]);
    }
}
