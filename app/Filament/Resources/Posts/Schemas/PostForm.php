<?php

namespace App\Filament\Resources\Posts\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Richer\RicherEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\PostCategory;
use App\Traits\HasSeo;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Thông tin chính ──────────────────────────────
                Section::make('Thông tin bài viết')
                    ->columns(2)
                    ->schema([
                        Select::make('post_category_id')
                            ->label('Danh mục')
                            ->options(
                                PostCategory::where('status', 1)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                // ── Ảnh ─────────────────────────────────────────
                Section::make('Hình ảnh')
                    ->columns(2)
                    ->schema([
                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện')
                            ->image(),

                        CuratorPicker::make('banner_id')
                            ->label('Ảnh Banner')
                            ->image(),
                    ]),

                // ── Nội dung ─────────────────────────────────────
                Section::make('Nội dung')
                    ->schema([
                        RicherEditor::make('content')
                            ->label('Nội dung')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                // ── Hiển thị ─────────────────────────────────────
                Section::make('Cài đặt hiển thị')
                    ->columns(2)
                    ->schema([
                        Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Nổi bật'),
                        Toggle::make('is_home')
                            ->label('Hiển thị trang chủ'),
                        Toggle::make('is_menu')
                            ->label('Hiển thị menu'),
                        Toggle::make('is_footer')
                            ->label('Hiển thị footer'),
                    ]),

                // ── SEO ──────────────────────────────────────────
                HasSeo::seoSection()->collapsed(),
            ]);
    }
}
