<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Thông tin Thẻ')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên thẻ')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Mô tả')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Hiển thị')
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Màu sắc')
                            ->required()
                            ->default('#6c757d'),
                            
                        TextInput::make('sort_order')
                            ->label('Thứ tự sắp xếp')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }
}
