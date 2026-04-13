<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin Menu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên menu')
                            ->placeholder('VD: Menu chính, Menu footer cột 2')
                            ->required()
                            ->maxLength(255),
                        Select::make('location')
                            ->label('Vị trí hiển thị')
                            ->options([
                                'header'  => 'Header (Menu chính)',
                                'footer'  => 'Footer',
                            ])
                            ->required()
                            ->helperText('Có thể tạo nhiều menu cho cùng 1 vị trí (VD: Footer cột 2, Footer cột 3).'),
                        Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
