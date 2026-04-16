<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use App\Filament\Forms\Components\ParentCategorySelect;
use App\Filament\Forms\Components\SlugInput;
use App\Models\ProjectCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Traits\HasSeo;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                Section::make('Thông tin danh mục')
                    ->schema([
                        ParentCategorySelect::make('parent_id')
                            ->treeModel(ProjectCategory::class)
                            ->rootAsZero('-- Danh mục gốc --'),

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
                            ->label('Ảnh đại diện'),
                        CuratorPicker::make('banner_id')
                            ->label('Banner'),

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
                    ])->columns(2),

                HasSeo::seoSection(),
            ]);
    }
}
