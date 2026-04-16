<?php

namespace App\Filament\Resources\FieldCategories\Schemas;

use App\Filament\Forms\Components\ParentCategorySelect;
use App\Filament\Forms\Components\SlugInput;
use App\Models\FieldCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FieldCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin danh mục lĩnh vực')->schema([
                    ParentCategorySelect::make('parent_id')
                        ->treeModel(FieldCategory::class)
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
                        ->label('Ảnh đại diện / Icon')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Mô tả ngắn')
                        ->rows(3)
                        ->columnSpanFull(),

                    RichEditor::make('content')
                        ->label('Nội dung chi tiết')
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Cài đặt')->schema([
                    TextInput::make('position')
                        ->label('Vị trí hiển thị (Position)')
                        ->numeric()
                        ->default(0),
                        
                    TextInput::make('order')
                        ->label('Thứ tự sắp xếp (Order)')
                        ->numeric()
                        ->default(0),

                    Toggle::make('status')
                        ->label('Kích hoạt')
                        ->default(true),
                ])->columns(3),
            ]);
    }
}
