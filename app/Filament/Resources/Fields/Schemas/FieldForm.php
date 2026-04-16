<?php

namespace App\Filament\Resources\Fields\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Models\FieldCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Traits\HasSeo;

class FieldForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin Lĩnh vực')->schema([
                    Select::make('field_category_id')
                        ->label('Danh mục lĩnh vực')
                        ->options(function () {
                            return FieldCategory::getLeafOptions();
                        })
                        ->searchable()
                        ->required(),

                    SlugInput::sourceField(TextInput::make('name'))
                        ->label('Tên lĩnh vực')
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

                    CuratorPicker::make('gallery')
                        ->label('Thư viện ảnh')
                        ->multiple()
                        ->columnSpanFull(),

                    Textarea::make('summary')
                        ->label('Mô tả ngắn (Summary)')
                        ->rows(3)
                        ->columnSpanFull(),

                    RichEditor::make('content')
                        ->label('Nội dung chi tiết')
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Cài đặt hiển thị')->schema([
                    Toggle::make('status')
                        ->label('Kích hoạt')
                        ->default(true)
                        ->required(),
                    Toggle::make('is_featured')
                        ->label('Nổi bật')
                        ->default(false)
                        ->required(),
                ])->columns(2),

                HasSeo::seoSection(),
            ]);
    }
}
