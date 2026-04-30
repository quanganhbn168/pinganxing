<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Forms\Components\FaqRepeater;
use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\Components\TagSelect;
use App\Models\PostCategory;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->components([
                Section::make('Thông tin bài viết')
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

                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                SlugInput::sourceField(TextInput::make('title'))
                                    ->label('Tiêu đề')
                                    ->required()
                                    ->maxLength(255),

                                SlugInput::make('slug'),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Nội dung')
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['paragraph', 'h2', 'h3'],
                                ['bulletList', 'orderedList'],
                                ['undo', 'redo'],
                            ])
                            ->floatingToolbars([
                                'paragraph' => ['bold', 'italic', 'underline', 'strike', 'link'],
                                'heading' => ['h2', 'h3'],
                                'list' => ['bulletList', 'orderedList'],
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make('Hình ảnh')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Ảnh đại diện')
                                    ->directory('posts'),

                                CuratorPicker::make('banner_id')
                                    ->label('Ảnh Banner')
                                    ->directory('posts'),
                            ])
                            ->columns(1),

                        Section::make('Cài đặt hiển thị')
                            ->schema([
                                Toggle::make('status')
                                    ->label('Kích hoạt')
                                    ->default(true),

                                Toggle::make('is_featured')
                                    ->label('Nổi bật'),

                                Toggle::make('is_home')
                                    ->label('Hiển thị trang chủ'),
                                TagSelect::make()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        HasSeo::seoSection(),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),

                FaqRepeater::make(),
            ]);
    }
}
