<?php

namespace App\Filament\Resources\FieldCategories\Schemas;

use App\Filament\Forms\Components\ParentCategorySelect;
use App\Filament\Forms\Components\SlugInput;
use App\Models\FieldCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FieldCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->components([
                Section::make('Thông tin danh mục lĩnh vực')
                    ->schema([
                        ParentCategorySelect::make('parent_id')
                            ->label('Danh mục cha')
                            ->treeModel(FieldCategory::class)
                            ->rootAsZero('-- Danh mục gốc --')
                            ->columnSpanFull(),

                        Grid::make([
                            'default' => 1,
                        ])
                            ->schema([
                                SlugInput::sourceField(TextInput::make('name'))
                                    ->label('Tên danh mục')
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
                            ->label('Nội dung chi tiết')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make('Ảnh đại diện / Icon')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Ảnh đại diện'),

                                CuratorPicker::make('banner_id')
                                    ->label('Ảnh banner'),

                            ]),

                        Section::make('Cài đặt')
                            ->schema([
                                TextInput::make('position')
                                    ->label('Vị trí hiển thị')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('order')
                                    ->label('Thứ tự sắp xếp')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('status')
                                    ->label('Kích hoạt')
                                    ->default(true),

                                Toggle::make('is_home')
                                    ->label('Hiện trang chủ')
                                    ->default(false),

                            ])
                            ->columns(1),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }
}
