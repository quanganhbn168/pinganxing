<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tài khoản')
                    ->schema([
                        TextInput::make('name')
                            ->label('Họ tên')
                            ->required(),

                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->required(),

                        TextInput::make('address')
                            ->label('Địa chỉ')
                            ->columnSpanFull(),

                        TextInput::make('email')
                            ->label('Email')
                            ->email(),

                        TextInput::make('avatar')
                            ->label('Avatar')
                            ->columnSpanFull(),

                        DateTimePicker::make('email_verified_at')
                            ->label('Thời gian xác thực email'),

                        TextInput::make('password')
                            ->label('Mật khẩu')
                            ->password(),
                    ])
                    ->columns(2),
            ]);
    }
}
