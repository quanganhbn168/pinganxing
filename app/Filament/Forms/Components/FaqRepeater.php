<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class FaqRepeater
{
    public static function make(string $name = 'faqs'): Repeater
    {
        return Repeater::make($name)
            ->label('Câu hỏi thường gặp')
            ->relationship('faqs')
            ->schema([
                TextInput::make('question')
                    ->label('Câu hỏi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->label('Câu trả lời')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->label('Hiển thị')
                    ->default(true),
            ])
            ->reorderable()
            ->orderColumn('position')
            ->collapsible()
            ->defaultItems(0)
            ->maxItems(20)
            ->columnSpanFull()
            ->addActionLabel('+ Thêm câu hỏi');
    }
}
