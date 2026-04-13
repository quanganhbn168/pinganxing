<?php

namespace App\Filament\Resources\PostCategories\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Models\PostCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                        Select::make('parent_id')
                            ->label('Danh mục cha')
                            ->placeholder('-- Danh mục gốc --')
                            ->options(function (?PostCategory $record) {
                                $query = PostCategory::query()->where('status', 1);
                                if ($record) {
                                    $excludeIds = array_merge([$record->id], $record->descendantIds());
                                    $query->whereNotIn('id', $excludeIds);
                                }
                                return $query->orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable(),

                        TextInput::make('name')
                            ->label('Tên danh mục')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(SlugInput::autoSlug()),

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
