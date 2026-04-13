<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\Components\SlugInput;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // ═════════════════════════════════════
                // ROW 1: Thông tin cơ bản + Sidebar
                // ═════════════════════════════════════
                Section::make('Thông tin cơ bản')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(SlugInput::autoSlug())
                            ->columnSpanFull(),
                        SlugInput::make('slug'),
                        TextInput::make('code')
                            ->label('Mã sản phẩm')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('brand_id')
                            ->label('Thương hiệu')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        ToggleButtons::make('product_type')
                            ->label('Loại hàng hóa')
                            ->options([
                                'physical' => 'Hàng hóa',
                                'service' => 'Dịch vụ',
                            ])
                            ->default('physical')
                            ->inline()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ═════════════════════════════════════
                // ROW 2: Ảnh & Media (aside layout)
                // ═════════════════════════════════════
                Section::make('Ảnh & Media')
                    ->schema([
                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện'),
                        CuratorPicker::make('banner_id')
                            ->label('Banner'),
                        CuratorPicker::make('gallery')
                            ->label('Thư viện ảnh')
                            ->multiple()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ═════════════════════════════════════
                // ROW 3: Giá & Kho (aside layout)
                // ═════════════════════════════════════
                Section::make('Giá & Kho')
                    ->schema([
                        TextInput::make('price')
                            ->label('Giá bán')
                            ->prefix('₫')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric(),
                        TextInput::make('stock')
                            ->label('Tồn kho')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_on_sale')
                            ->label('Đang giảm giá')
                            ->live()
                            ->afterStateUpdated(function (bool $state, callable $set) {
                                if (! $state) {
                                    $set('price_discount', null);
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('price_discount')
                            ->label('Giá khuyến mãi')
                            ->prefix('₫')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->visible(fn (callable $get) => (bool) $get('is_on_sale'))
                            ->requiredIf('is_on_sale', true)
                            ->helperText('Nhập giá khuyến mãi (thấp hơn giá bán).'),
                    ])
                    ->columns(2),

                // ═════════════════════════════════════
                // ROW 4: Mô tả chi tiết
                // ═════════════════════════════════════
                Section::make('Mô tả sản phẩm')
                    ->schema([
                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->rows(3)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Nội dung chi tiết')
                            ->columnSpanFull(),
                        RichEditor::make('specifications')
                            ->label('Thông số kỹ thuật')
                            ->columnSpanFull(),
                    ]),

                // ═════════════════════════════════════
                // ROW 5: Trạng thái (aside compact)
                // ═════════════════════════════════════
                Section::make('Trạng thái & Hiển thị')
                    ->schema([
                        Toggle::make('status')
                            ->label('Kích hoạt')
                            ->default(true),
                        Toggle::make('has_variants')
                            ->label('Có biến thể')
                            ->helperText('Sản phẩm có nhiều phiên bản (màu, size...)'),
                        Toggle::make('is_featured')
                            ->label('Sản phẩm nổi bật'),
                        Toggle::make('is_home')
                            ->label('Hiện trang chủ'),
                    ])
                    ->columns(4),

                // ═════════════════════════════════════
                // ROW 6: SEO (collapsed by default)
                // ═════════════════════════════════════
                HasSeo::seoSection(),
            ]);
    }
}
