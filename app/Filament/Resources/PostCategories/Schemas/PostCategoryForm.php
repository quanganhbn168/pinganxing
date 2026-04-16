<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Forms\Components\ParentCategorySelect;
use App\Filament\Forms\Components\SlugInput;
use App\Models\PostCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Thông tin danh mục')
                    ->schema([
                        ParentCategorySelect::make('parent_id')
                            ->treeModel(PostCategory::class)
                            ->rootAsNull('-- Danh mục gốc --'),

                        SlugInput::sourceField(TextInput::make('name'))
                            ->label('Tên danh mục')
                            ->required()
                            ->maxLength(255),

                        Hidden::make('__slug_locked')
                            ->default(false)
                            ->dehydrated(false),

                        Hidden::make('__slug_last_auto')
                            ->default(null)
                            ->dehydrated(false),

                        SlugInput::make('slug'),

                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện')
                            ->directory('post-categories')
                            ->multiple(false)
                            ->columnSpanFull(),
                        CuratorPicker::make('banner_id')
                            ->label('Banner')
                            ->directory('post-categories')
                            ->multiple(false)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Nội dung chi tiết')
                            ->columnSpanFull(),

                        Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true),
                    ]),

                \App\Traits\HasSeo::seoSection(),
            ]);
    }
}
