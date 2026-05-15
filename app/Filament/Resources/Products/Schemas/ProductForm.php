<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\Components\FaqRepeater;
use App\Filament\Forms\Components\MoneyInput;
use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\Components\TagSelect;
use App\Models\Attribute;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action as FieldAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 3,
                ])
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make('Thông tin cơ bản')
                                    ->description('Định danh sản phẩm, phân loại danh mục và loại hàng hóa.')
                                    ->schema([
                                        Hidden::make('type')
                                            ->default('simple')
                                            ->dehydrated(true),

                                        SlugInput::sourceField(TextInput::make('name'))
                                            ->label('Tên sản phẩm')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        SlugInput::make('slug')
                                            ->columnSpanFull(),

                                        TextInput::make('code')
                                            ->label('Mã sản phẩm')
                                            ->placeholder('VD: CAM-IP-4MP-01')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50)
                                            ->suffixAction(
                                                FieldAction::make('generateProductCode')
                                                    ->icon('heroicon-o-sparkles')
                                                    ->tooltip('Tạo mã ngẫu nhiên')
                                                    ->action(function (Set $set): void {
                                                        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

                                                        $set('code', 'SP-' . now()->format('ymd') . '-' . $random);
                                                    })
                                            ),

                                        Select::make('category_id')
                                            ->label('Danh mục')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->required()
                                            ->afterStateUpdated(function (Set $set, ?int $state): void {
                                                if (! static::categoryHasVariantAttributes($state)) {
                                                    $set('has_variants', false);
                                                    $set('type', 'simple');
                                                }
                                            }),

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

                                Section::make('Giá bán và tồn kho')
                                    ->description('Với sản phẩm có biến thể, giá và kho sẽ quản lý theo từng biến thể.')
                                    ->schema([
                                        Toggle::make('has_variants')
                                            ->label('Có biến thể')
                                            ->disabled(fn (Get $get): bool => ! static::categoryHasVariantAttributes((int) ($get('category_id') ?? 0)))
                                            ->helperText(fn (Get $get): string => static::categoryHasVariantAttributes((int) ($get('category_id') ?? 0))
                                                ? 'Bật nếu sản phẩm có nhiều phiên bản (màu, dung lượng, chuẩn kết nối...).'
                                                : 'Danh mục hiện tại chưa có thuộc tính dùng cho biến thể. Hãy cấu hình thuộc tính ở danh mục trước.')
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, bool $state): void {
                                                $set('type', $state ? 'variable' : 'simple');

                                                if ($state) {
                                                    $set('is_on_sale', false);
                                                    $set('price_discount', null);
                                                }
                                            })
                                            ->columnSpanFull(),

                                        MoneyInput::make('price')
                                            ->label('Giá bán')
                                            ->zeroWhenEmpty()
                                            ->required(fn (Get $get): bool => ! (bool) $get('has_variants'))
                                            ->disabled(fn (Get $get): bool => (bool) $get('has_variants'))
                                            ->helperText(fn (Get $get): ?string => (bool) $get('has_variants')
                                                ? 'Sản phẩm có biến thể: giá sẽ quản lý theo từng biến thể.'
                                                : null),

                                        TextInput::make('stock')
                                            ->label('Tồn kho')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required(fn (Get $get): bool => ! (bool) $get('has_variants'))
                                            ->disabled(fn (Get $get): bool => (bool) $get('has_variants'))
                                            ->helperText(fn (Get $get): ?string => (bool) $get('has_variants')
                                                ? 'Sản phẩm có biến thể: tồn kho sẽ quản lý theo từng biến thể.'
                                                : null),

                                        Toggle::make('is_on_sale')
                                            ->label('Đang giảm giá')
                                            ->live()
                                            ->afterStateUpdated(function (bool $state, Set $set): void {
                                                if (! $state) {
                                                    $set('price_discount', null);
                                                }
                                            })
                                            ->disabled(fn (Get $get): bool => (bool) $get('has_variants'))
                                            ->hidden(fn (Get $get): bool => (bool) $get('has_variants'))
                                            ->columnSpanFull(),

                                        MoneyInput::make('price_discount')
                                            ->label('Giá khuyến mãi')
                                            ->visible(fn (Get $get): bool => (bool) $get('is_on_sale') && ! (bool) $get('has_variants'))
                                            ->required(fn (Get $get): bool => (bool) $get('is_on_sale') && ! (bool) $get('has_variants'))
                                            ->lt('price')
                                            ->helperText('Nhập giá khuyến mãi thấp hơn giá bán.'),
                                    ])
                                    ->columns(2),

                                Section::make('Mô tả sản phẩm')
                                    ->description('Nội dung hiển thị ở trang chi tiết sản phẩm.')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Mô tả ngắn')
                                            ->placeholder('Tóm tắt ngắn gọn điểm nổi bật của sản phẩm...')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        RichEditor::make('content')
                                            ->label('Nội dung chi tiết')
                                            ->columnSpanFull(),

                                        RichEditor::make('specifications')
                                            ->label('Thông số kỹ thuật')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 2,
                            ]),

                        Group::make()
                            ->schema([
                                Section::make('Ảnh và media')
                                    ->description('Ảnh đại diện, banner và thư viện ảnh.')
                                    ->schema([
                                        CuratorPicker::make('image_id')
                                            ->label('Ảnh đại diện')
                                            ->buttonLabel('Chọn / Tải ảnh')
                                            ->constrained(true)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                            ->maxSize(2048)
                                            ->helperText('Nên dùng ảnh vuông hoặc tỉ lệ 4:3. Tối đa 2MB.'),

                                        CuratorPicker::make('banner_id')
                                            ->label('Banner')
                                            ->buttonLabel('Chọn / Tải banner')
                                            ->constrained(true)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(4096)
                                            ->helperText('Nên dùng banner tỉ lệ 16:9 hoặc 21:9. Tối đa 4MB.'),

                                        CuratorPicker::make('gallery')
                                            ->label('Thư viện ảnh')
                                            ->buttonLabel('Chọn / Tải nhiều ảnh')
                                            ->constrained(true)
                                            ->multiple()
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(4096)
                                            ->helperText('Có thể chọn nhiều ảnh cho trang chi tiết sản phẩm.'),
                                    ])
                                    ->columns(1)
                                    ->collapsible(),

                                Section::make('Trạng thái & Hiển thị')
                                    ->description('Thiết lập trạng thái và vị trí hiển thị.')
                                    ->schema([
                                        Toggle::make('status')
                                            ->label('Kích hoạt')
                                            ->default(true),

                                        Toggle::make('is_featured')
                                            ->label('Sản phẩm nổi bật')
                                            ->helperText('Ưu tiên hiển thị trong các khu vực nổi bật.'),

                                        Toggle::make('is_home')
                                            ->label('Hiện trang chủ')
                                            ->helperText('Hiển thị sản phẩm ở khối trang chủ.'),

                                        TagSelect::make(),
                                    ])
                                    ->columns(1),

                                FaqRepeater::make(),

                                HasSeo::seoSection(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 1,
                            ]),
                    ]),
            ]);
    }

    protected static function categoryHasVariantAttributes(?int $categoryId): bool
    {
        if (! $categoryId) {
            return false;
        }

        return Attribute::query()
            ->where('is_variant_defining', true)
            ->whereHas('categories', fn ($query) => $query->where('categories.id', $categoryId))
            ->exists();
    }
}