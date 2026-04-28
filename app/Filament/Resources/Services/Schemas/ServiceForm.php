<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Models\ServiceCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Traits\HasSeo;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin chính')
                    ->schema([
                        Select::make('service_category_id')
                            ->label('Danh mục dịch vụ')
                            ->options(function () {
                                return ServiceCategory::getLeafOptions();
                            })
                            ->searchable()
                            ->required(),

                        SlugInput::sourceField(TextInput::make('name'))
                            ->label('Tên dịch vụ')
                            ->required()
                            ->maxLength(255),

                        SlugInput::make('slug'),

                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện / Icon')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Nội dung dịch vụ')
                    ->schema([
                        CuratorPicker::make('banner_id')
                            ->label('Banner')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Nội dung chi tiết')
                            ->columnSpanFull(),

                        CuratorPicker::make('gallery')
                            ->label('Thư viện ảnh')
                            ->multiple()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Liên kết Landing Page')
                    ->description('Chọn các thực thể liên quan để hiển thị ở đáy trang (dạng Mini-landing page).')
                    ->schema([
                        Select::make('projects')
                            ->label('Dự án đã triển khai')
                            ->multiple()
                            ->relationship('projects', 'name')
                            ->preload(),
                        Select::make('products')
                            ->label('Sản phẩm / Phân hệ')
                            ->multiple()
                            ->relationship('products', 'name')
                            ->preload(),
                        Select::make('posts')
                            ->label('Bài viết tham khảo')
                            ->multiple()
                            ->relationship('posts', 'title')
                            ->preload(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Hiển thị & SEO')
                    ->schema([
                        Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true),
                        Toggle::make('is_home')
                            ->label('Hiển thị Trang chủ')
                            ->default(false),
                        Toggle::make('is_menu')
                            ->label('Hiển thị Menu')
                            ->default(false),
                        Toggle::make('is_footer')
                            ->label('Hiển thị Footer')
                            ->default(false),
                        HasSeo::seoSection(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }
}
