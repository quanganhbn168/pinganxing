<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Forms\Components\FaqRepeater;
use App\Filament\Forms\Components\SlugInput;
use App\Filament\Forms\Components\TagSelect;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\ProjectCategory;

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
                        SlugInput::make('slug')
                            ->columnSpanFull(),
                        Select::make('project_category_id')
                            ->label('Danh mục')
                            ->options(fn() => ProjectCategory::getLeafOptions())
                            ->searchable()
                            ->preload()
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
                Section::make('Nội dung trình bày dự án')
                    ->description('Các trường này dùng cho trang chi tiết dự án theo cấu trúc case study.')
                    ->schema([
                        RichEditor::make('project_overview')
                            ->label('Tổng quan dự án')
                            ->columnSpanFull(),

                        Repeater::make('business_problems')
                            ->label('Bài toán doanh nghiệp')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->searchable()
                                    ->default('fas fa-triangle-exclamation'),
                                TextInput::make('title')
                                    ->label('Ý chính')
                                    ->placeholder('Dữ liệu vận hành phân tán')
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
                            ->addActionLabel('+ Thêm bài toán'),

                        Repeater::make('implemented_solutions')
                            ->label('Giải pháp triển khai')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->searchable()
                                    ->default('fas fa-screwdriver-wrench'),
                                TextInput::make('title')
                                    ->label('Ý chính')
                                    ->placeholder('Chuẩn hóa quy trình bán hàng và kho')
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
                            ->addActionLabel('+ Thêm giải pháp'),

                        Repeater::make('implementation_process')
                            ->label('Quy trình triển khai')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(self::iconOptions())
                                    ->searchable()
                                    ->default('fas fa-circle-check'),
                                TextInput::make('title')
                                    ->label('Tên bước')
                                    ->placeholder('Khảo sát hiện trạng')
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

                        Repeater::make('achieved_results')
                            ->label('Kết quả đạt được')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Số liệu')
                                    ->placeholder('+35%'),
                                TextInput::make('label')
                                    ->label('Nhãn')
                                    ->placeholder('Hiệu suất vận hành')
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
                            ->addActionLabel('+ Thêm kết quả'),
                    ])
                    ->columns(2),
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
                        TagSelect::make()
                            ->columnSpanFull(),
                    ]),
                Section::make('Ảnh đại diện & Bìa')
                    ->schema([
                        CuratorPicker::make('image_id')
                            ->label('Ảnh đại diện (Thumbnail)'),
                        CuratorPicker::make('banner_id')
                            ->label('Ảnh Bìa / Banner'),
                    ])->columns(2),

                FaqRepeater::make(),

                HasSeo::seoSection(),
            ]);
    }

    private static function iconOptions(): array
    {
        return [
            'fas fa-triangle-exclamation' => 'Cảnh báo / Bài toán',
            'fas fa-screwdriver-wrench' => 'Triển khai',
            'fas fa-chart-line' => 'Tăng trưởng',
            'fas fa-chart-pie' => 'Báo cáo',
            'fas fa-cash-register' => 'POS / Bán hàng',
            'fas fa-boxes-stacked' => 'Kho hàng',
            'fas fa-users' => 'Khách hàng / Nhân sự',
            'fas fa-store' => 'Cửa hàng',
            'fas fa-warehouse' => 'Kho vận',
            'fas fa-shield-halved' => 'Bảo mật',
            'fas fa-clipboard-check' => 'Khảo sát',
            'fas fa-lightbulb' => 'Đề xuất',
            'fas fa-file-signature' => 'Ký kết',
            'fas fa-chalkboard-user' => 'Đào tạo',
            'fas fa-circle-check' => 'Hoàn tất / Nghiệm thu',
            'fas fa-headset' => 'Hỗ trợ',
        ];
    }
}
