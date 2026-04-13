<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Models\Category;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
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
                            ->options(function (?Category $record) {
                                $query = Category::query()->where('status', 1);
                                if ($record) {
                                    $excludeIds = array_merge([$record->id], $record->getAllDescendantIds());
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
                            ->live(onBlur: true)
                            ->afterStateUpdated(SlugInput::autoSlug()),

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
                    ]),

                Section::make('Thuộc tính sản phẩm')
                    ->description('Chọn các thuộc tính mà sản phẩm thuộc danh mục này có thể dùng để tạo biến thể.')
                    ->schema([
                        CheckboxList::make('attributes')
                            ->label('Thuộc tính')
                            ->relationship('attributes', 'name')
                            ->columns(3)
                            ->helperText('VD: Màu sắc, Kích thước, RAM... Những thuộc tính này sẽ hiển thị khi tạo biến thể sản phẩm.'),
                    ]),

                \App\Traits\HasSeo::seoSection(),
            ]);
    }
}
