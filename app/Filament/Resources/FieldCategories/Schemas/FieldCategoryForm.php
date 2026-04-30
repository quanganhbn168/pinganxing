<?php

namespace App\Filament\Resources\FieldCategories\Schemas;

use App\Filament\Forms\Components\FaqRepeater;
use App\Filament\Forms\Components\ParentCategorySelect;
use App\Filament\Forms\Components\SlugInput;
use App\Models\FieldCategory;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
                                    ->label('Lĩnh vực tiêu biểu')
                                    ->helperText('Ưu tiên dùng làm block nổi bật ở đầu trang Lĩnh vực.')
                                    ->default(false),

                            ])
                            ->columns(1),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),

                Section::make('Landing page lĩnh vực')
                    ->description('Các khối hiển thị dưới leaderboard: tổng quan, thách thức, giải pháp, tính năng, hiệu quả, quy trình và FAQ.')
                    ->schema([
                        Textarea::make('solution_overview')
                            ->label('Tổng quan giải pháp')
                            ->helperText('Hiển thị trong block lĩnh vực tiêu biểu. Nếu trống sẽ dùng mô tả ngắn/nội dung chi tiết.')
                            ->rows(4)
                            ->columnSpanFull(),

                        Repeater::make('business_challenges')
                            ->label('Thách thức doanh nghiệp')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Ý chính')
                                    ->placeholder('Quản lý nhiều cửa hàng, dữ liệu phân tán')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(8)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm thách thức'),

                        Repeater::make('cnetpos_solutions')
                            ->label('Giải pháp của CNETPOS')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Ý chính')
                                    ->placeholder('Quản trị tập trung mọi chi nhánh')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(8)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm giải pháp'),

                        Repeater::make('key_features')
                            ->label('Tính năng nổi bật')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->searchable()
                                    ->default('fas fa-layer-group')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Tên tính năng')
                                    ->placeholder('Quản lý bán hàng POS')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(10)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm tính năng'),

                        Repeater::make('impact_stats')
                            ->label('Hiệu quả đạt được')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Số liệu')
                                    ->placeholder('+35%')
                                    ->required(),
                                TextInput::make('label')
                                    ->label('Nhãn')
                                    ->placeholder('Doanh thu bình quân')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(6)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm chỉ số'),

                        Repeater::make('implementation_steps')
                            ->label('Quy trình triển khai giải pháp')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->searchable()
                                    ->default('fas fa-circle-check')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Tên bước')
                                    ->placeholder('Khảo sát & Tư vấn')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(8)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm bước'),

                        FaqRepeater::make(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    private static function iconOptions(): array
    {
        return [
            'fas fa-layer-group' => 'Layer group',
            'fas fa-cash-register' => 'POS / Bán hàng',
            'fas fa-chart-line' => 'Tăng trưởng',
            'fas fa-chart-pie' => 'Báo cáo',
            'fas fa-boxes-stacked' => 'Kho hàng',
            'fas fa-users' => 'Khách hàng / Nhân sự',
            'fas fa-shield-halved' => 'Bảo mật',
            'fas fa-store' => 'Cửa hàng',
            'fas fa-warehouse' => 'Kho vận',
            'fas fa-clipboard-check' => 'Khảo sát',
            'fas fa-lightbulb' => 'Đề xuất',
            'fas fa-file-signature' => 'Ký kết',
            'fas fa-chalkboard-user' => 'Đào tạo',
            'fas fa-circle-check' => 'Nghiệm thu',
            'fas fa-headset' => 'Hỗ trợ',
        ];
    }
}
