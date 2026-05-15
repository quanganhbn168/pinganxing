<?php

namespace App\Filament\Resources\Brands\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->label('Tên thương hiệu')
                ->required(),
                TextInput::make('link')
                ->label('Link website'),
                CuratorPicker::make('image_id')
                ->label('Logo / Ảnh thương hiệu')
                ->buttonLabel('Tải lên / Chọn ảnh')
                ->constrained(true)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                ->maxSize(2048)
                ->columnSpanFull(),
                Toggle::make('status')
                ->label('Hiển thị')
                ->default(true)
                ->required(),
            ]);
    }
}
