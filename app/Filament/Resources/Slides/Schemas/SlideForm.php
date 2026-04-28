<?php

namespace App\Filament\Resources\Slides\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Slide;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class SlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('title')
                    ->label('Tiêu đề')
                    ->maxLength(255)
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('subtitle')
                    ->label('Tiêu đề phụ')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Mô tả')
                    ->columnSpanFull()
                    ->rows(3),
                TextInput::make('link')
                    ->label('Liên kết')
                    ->maxLength(255),
                TextInput::make('button_text')
                    ->label('Nội dung nút')
                    ->maxLength(255),
                CuratorPicker::make('image_id')
                    ->label('Ảnh Slide / Banner')
                    ->buttonLabel('Tải lên / Chọn ảnh')
                    ->helperText('Ảnh bắt buộc. Kích thước khuyên dùng cho slide trang chủ: 1920x1080 hoặc rộng hơn theo tỷ lệ 16:9.')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('position')
                    ->label('Thứ tự sắp xếp')
                    ->required()
                    ->numeric()
                    ->default(fn() => Slide::max('position') + 1 ?: 1),
                Toggle::make('status')
                    ->label("Hiển thị")
                    ->default(true)
                    ->required(),
            ]);
    }
}
