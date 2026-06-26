<?php

namespace App\Filament\Resources\Intros\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IntroForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin giới thiệu')
                    ->schema([
                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required(),

                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required(),

                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label('Nội dung')
                            ->rows(6)
                            ->columnSpanFull(),

                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->columnSpanFull(),

                        TextInput::make('banner')
                            ->label('Banner'),

                        Toggle::make('status')
                            ->label('Hiển thị')
                            ->required(),

                        Toggle::make('is_home')
                            ->label('Hiện trang chủ')
                            ->required(),

                        Toggle::make('is_main')
                            ->label('Nội dung chính')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
