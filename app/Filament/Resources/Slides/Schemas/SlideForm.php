<?php

namespace App\Filament\Resources\Slides\Schemas;

use App\Enums\SliderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use App\Models\Slide;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class SlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title'),
                TextInput::make('subtitle'),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('link'),
                TextInput::make('button_text'),
                CuratorPicker::make('image_id')
                    ->label('Ảnh Slide / Banner')
                    ->buttonLabel('Tải lên / Chọn ảnh')
                    ->helperText('Hệ thống tích hợp sẵn Curator Media. Anh có thể crop ảnh trực tiếp trong popup. Kích thước khuyên dùng: Trang chủ (1920x1080), Đối tác (800x400).')
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Loại')
                    ->options(SliderType::class)
                    ->default(SliderType::HOME)
                    ->required(),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(fn() => Slide::max('position') + 1 ?: 1),
                Toggle::make('status')
                    ->label("Hiển thị")
                    ->default(true)
                    ->required(),
                Hidden::make('is_home')
                    ->default(false),
            ]);
    }
}
