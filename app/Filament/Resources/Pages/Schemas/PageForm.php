<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use App\Filament\Forms\Components\SlugInput;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpan('full')
                    ->schema([
                        Group::make()->schema([
                            Section::make('Nội dung trang')->schema([
                                SlugInput::sourceField(TextInput::make('title'))
                                    ->label('Tên trang')
                                    ->required()
                                    ->maxLength(255),

                                SlugInput::make('slug'),

                                RichEditor::make('content')
                                    ->label('Nội dung văn bản')
                                    ->columnSpanFull(),
                            ])
                        ])->columnSpan(['default' => 3, 'lg' => 2]),

                        Group::make()->schema([
                            Section::make('Trạng thái')->schema([
                                Toggle::make('status')
                                    ->label('Hiển thị công khai')
                                    ->default(true),
                            ]),

                            Section::make('Tối ưu SEO')->schema([
                                Textarea::make('meta_description')
                                    ->label('Mô tả SEO (Meta Description)')
                                    ->rows(3)
                                    ->maxLength(500),

                                TextInput::make('meta_keywords')
                                    ->label('Từ khóa (Meta Keywords)')
                                    ->maxLength(255),

                                CuratorPicker::make('meta_image_id')
                                    ->label('Ảnh chia sẻ (Meta Image)')
                                    ->buttonLabel('Chọn ảnh'),
                            ]),
                        ])->columnSpan(['default' => 3, 'lg' => 1]),
                    ]),
            ]);
    }
}