<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use App\Traits\HasSeo;
use App\Filament\Forms\Components\SlugInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                    Section::make('Thông tin cơ bản')
                            ->schema([
                                SlugInput::sourceField(TextInput::make('name'))
                                    ->label('Tên dự án')
                                    ->required()
                                    ->columnSpanFull(),
                                Hidden::make('__slug_locked')
                                    ->default(false)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                                Hidden::make('__slug_last_auto')
                                    ->default(null)
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                                SlugInput::make('slug')
                                    ->columnSpanFull(),
                                Select::make('project_category_id')
                                    ->label('Danh mục')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required()
                                    ->columnSpanFull(),
                                    TextInput::make('investor')->label('Chủ đầu tư'),
                                    TextInput::make('address')->label('Địa chỉ/Địa điểm'),
                                    TextInput::make('year')->label('Năm thực hiện'),
                                    TextInput::make('value')->label('Giá trị dự án'),
                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(3)
                                    ->required()
                                    ->columnSpanFull(),
                            ])->columns(2),
                        Section::make('Nội dung chi tiết')
                            ->schema([
                                RichEditor::make('content')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Thư viện ảnh')
                            ->schema([
                                CuratorPicker::make('gallery')
                                    ->hiddenLabel()
                                    ->multiple()
                                    ->columnSpanFull(),
                            ]),
                            
                        Section::make('Cài đặt & Trạng thái')
                            ->schema([
                                Toggle::make('status')
                                    ->label('Kích hoạt')
                                    ->default(true)
                                    ->required(),
                                Toggle::make('is_home')
                                    ->label('Hiển thị Nổi bật (Trang chủ)')
                                    ->default(false)
                                    ->required(),
                                Select::make('tags')
                                    ->label('Gắn Thẻ (Tags)')
                                    ->relationship('tags', 'name')
                                    ->multiple()
                                    ->preload(),
                            ]),
                        Section::make('Ảnh đại diện & Bìa')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Ảnh đại diện (Thumbnail)'),
                                CuratorPicker::make('banner_id')
                                    ->label('Ảnh Bìa / Banner'),
                            ])->columns(2),

                        HasSeo::seoSection(),
            ]);
    }
}
