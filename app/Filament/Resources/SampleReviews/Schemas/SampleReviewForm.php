<?php

namespace App\Filament\Resources\SampleReviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SampleReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Nội dung Mẫu Đánh Giá')
                    ->schema([
                        \Filament\Forms\Components\Select::make('rating')
                            ->label('Mức sao tương ứng')
                            ->options([
                                5 => '5 Sao (Rất tốt)',
                                4 => '4 Sao (Tốt)',
                                3 => '3 Sao (Trung bình)',
                                2 => '2 Sao (Kém)',
                                1 => '1 Sao (Rất kém)',
                            ])
                            ->required()
                            ->default(5)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Textarea::make('content')
                            ->label('Nội dung mẫu')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Trạng thái Kích hoạt')
                            ->default(true)
                            ->required(),
                    ])
            ]);
    }
}
