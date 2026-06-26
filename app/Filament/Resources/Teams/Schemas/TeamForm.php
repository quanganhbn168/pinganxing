<?php

namespace App\Filament\Resources\Teams\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin thành viên')
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ tên')
                            ->required(),

                        TextInput::make('position')
                            ->label('Chức vụ'),

                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->columnSpanFull(),

                        TextInput::make('level')
                            ->label('Cấp bậc'),

                        TextInput::make('experience')
                            ->label('Kinh nghiệm')
                            ->numeric(),

                        Textarea::make('bio')
                            ->label('Giới thiệu')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
