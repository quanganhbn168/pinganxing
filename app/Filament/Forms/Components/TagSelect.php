<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TagSelect
{
    public static function make(string $name = 'tags'): Select
    {
        return Select::make($name)
            ->label('Gắn thẻ')
            ->relationship('tags', 'name', modifyQueryUsing: fn ($query) => $query->ordered())
            ->multiple()
            ->preload()
            ->searchable()
            ->createOptionForm(static::formSchema())
            ->createOptionModalHeading('Tạo thẻ mới');
    }

    public static function formSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên thẻ')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            ColorPicker::make('color')
                ->label('Màu sắc')
                ->required()
                ->default('#6c757d'),

            TextInput::make('sort_order')
                ->label('Thứ tự sắp xếp')
                ->numeric()
                ->default(0),

            Textarea::make('description')
                ->label('Mô tả')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }
}
