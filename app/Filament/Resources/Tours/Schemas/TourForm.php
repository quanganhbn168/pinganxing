<?php

namespace App\Filament\Resources\Tours\Schemas;

use App\Models\Tour;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->components([
                Section::make('Thông tin tour')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên tour')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                TextInput::make('slug')
                                    ->label('Đường dẫn')
                                    ->required()
                                    ->unique(Tour::class, 'slug', ignoreRecord: true),

                                Select::make('tour_category_id')
                                    ->label('Điểm đến / Danh mục')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('code')
                                    ->label('Mã tour'),

                                TextInput::make('duration')
                                    ->label('Thời gian (VD: 3N2Đ)'),

                                TextInput::make('departure')
                                    ->label('Lịch khởi hành (VD: Hằng ngày)'),

                                TextInput::make('transport')
                                    ->label('Phương tiện (VD: Máy bay)'),

                                TagsInput::make('features')
                                    ->label('Điểm nổi bật')
                                    ->placeholder('Thêm điểm nổi bật... (nhấn Enter)')
                                    ->columnSpanFull(),

                                TextInput::make('price')
                                    ->label('Giá gốc')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('price_discount')
                                    ->label('Giá khuyến mãi')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('rating')
                                    ->label('Điểm đánh giá (1.0 - 5.0)')
                                    ->numeric()
                                    ->step('0.1')
                                    ->default(5.0),

                                TextInput::make('review_count')
                                    ->label('Số lượt đánh giá')
                                    ->numeric()
                                    ->default(0),

                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('Chương trình chi tiết')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('tours/content')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull(),
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
                                    ->label('Ảnh đại diện (Card)')
                                    ->buttonLabel('Chọn ảnh')
                                    ->columnSpanFull(),

                                CuratorPicker::make('banner_id')
                                    ->label('Ảnh Banner chi tiết')
                                    ->buttonLabel('Chọn ảnh')
                                    ->columnSpanFull(),

                                CuratorPicker::make('gallery')
                                    ->label('Thư viện ảnh')
                                    ->multiple()
                                    ->buttonLabel('Chọn nhiều ảnh')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Trạng thái')
                            ->schema([
                                Toggle::make('status')
                                    ->label('Hiển thị')
                                    ->default(true),

                                Toggle::make('is_home')
                                    ->label('Hiện ở trang chủ')
                                    ->default(false),

                                Toggle::make('is_hot')
                                    ->label('Tour HOT')
                                    ->default(false),

                                Toggle::make('is_sale')
                                    ->label('Giảm giá')
                                    ->default(false),
                            ]),

                        HasSeo::seoSection(),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }
}
