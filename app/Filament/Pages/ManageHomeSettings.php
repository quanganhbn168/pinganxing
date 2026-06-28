<?php

namespace App\Filament\Pages;

use App\Settings\HomeSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use UnitEnum;

class ManageHomeSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Cấu hình';
    protected static ?string $navigationLabel = 'Trang chủ';
    protected static ?string $title = 'Cài đặt Trang chủ';
    protected static ?int $navigationSort = 13;

    protected static string $settings = HomeSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('HomeSettings')
                    ->tabs([
                        // ═══════════════════════════════════════
                        // TAB 1: KHỐI GIỚI THIỆU
                        // ═══════════════════════════════════════
                        Tab::make('Giới thiệu')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Nội dung giới thiệu')
                                    ->description('Khối giới thiệu ngắn hiển thị trên trang chủ (section 3)')
                                    ->schema([
                                        TextInput::make('intro_title')
                                            ->label('Tiêu đề')
                                            ->columnSpanFull(),
                                        RichEditor::make('intro_description')
                                            ->label('Mô tả')
                                            ->columnSpanFull(),
                                        CuratorPicker::make('intro_image')
                                            ->label('Hình ảnh minh họa')
                                            ->acceptedFileTypes(['image/*'])
                                            ->columnSpanFull(),
                                    ])->columns(1),

                                Section::make('Điểm nổi bật')
                                    ->description('Các khối icon + tiêu đề + mô tả hiển thị bên cạnh ảnh giới thiệu.')
                                    ->schema([
                                        Repeater::make('intro_features')
                                            ->label('')
                                            ->schema([
                                                Select::make('icon')
                                                    ->label('Icon')
                                                    ->options([
                                                        'fas fa-shield-alt'      => 'Khiên — Bảo mật',
                                                        'fas fa-bolt'            => 'Sét — Tốc độ / Hiệu suất',
                                                        'fas fa-cogs'            => 'Bánh răng — Công nghệ',
                                                        'fas fa-chart-line'      => 'Biểu đồ — Tăng trưởng',
                                                        'fas fa-lock'            => 'Khóa — An toàn',
                                                        'fas fa-rocket'          => 'Tên lửa — Khởi động',
                                                        'fas fa-users'           => 'Nhóm người — Đội ngũ',
                                                        'fas fa-headset'         => 'Tai nghe — Hỗ trợ',
                                                        'fas fa-award'           => 'Huy chương — Giải thưởng',
                                                        'fas fa-check-circle'    => 'Dấu tích — Đảm bảo',
                                                        'fas fa-globe'           => 'Quả cầu — Toàn cầu',
                                                        'fas fa-database'        => 'Cơ sở dữ liệu — Lưu trữ',
                                                        'fas fa-cloud'           => 'Đám mây — Cloud',
                                                        'fas fa-sync-alt'        => 'Vòng xoay — Đồng bộ',
                                                        'fas fa-mobile-alt'      => 'Điện thoại — Di động',
                                                        'fas fa-laptop-code'     => 'Laptop — Phần mềm',
                                                        'fas fa-handshake'       => 'Bắt tay — Hợp tác',
                                                        'fas fa-dollar-sign'     => 'Đô la — Tiết kiệm',
                                                        'fas fa-lightbulb'       => 'Bóng đèn — Sáng tạo',
                                                        'fas fa-tools'           => 'Dụng cụ — Bảo trì',
                                                        'fas fa-project-diagram' => 'Sơ đồ — Quy trình',
                                                        'fas fa-server'          => 'Máy chủ — Server',
                                                        'fas fa-wifi'            => 'Wifi — Kết nối',
                                                        'fas fa-chart-bar'       => 'Cột biểu đồ — Báo cáo',
                                                    ])
                                                    ->searchable()
                                                    ->required(),
                                                TextInput::make('title')
                                                    ->label('Tiêu đề')
                                                    ->placeholder('Bảo Mật Cấp Doanh Nghiệp')
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->label('Mô tả ngắn')
                                                    ->rows(2)
                                                    ->placeholder('Kiến trúc bảo mật đa lớp...'),
                                            ])
                                            ->columns(3)
                                            ->reorderable()
                                            ->collapsible()
                                            ->defaultItems(2)
                                            ->maxItems(6)
                                            ->columnSpanFull()
                                            ->addActionLabel('+ Thêm điểm nổi bật'),
                                    ]),

                                Section::make('Video giới thiệu')
                                    ->description('Nếu có video, nút Play sẽ hiển thị đè lên ảnh minh họa.')
                                    ->icon('heroicon-o-play-circle')
                                    ->schema([
                                        TextInput::make('video_title')
                                            ->label('Tiêu đề video')
                                            ->columnSpanFull(),
                                        TextInput::make('video_url')
                                            ->label('Link YouTube')
                                            ->helperText('VD: https://www.youtube.com/watch?v=xxxx'),
                                        CuratorPicker::make('video_file')
                                            ->label('Hoặc Upload video trực tiếp')
                                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime']),
                                    ])->columns(2),
                            ]),

                        // ═══════════════════════════════════════
                        // TAB 2: COUNTER THỐNG KÊ
                        // ═══════════════════════════════════════
                        Tab::make('Counter (Thống kê)')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Repeater::make('counters')
                                    ->label('Các ô thống kê')
                                    ->schema([
                                        Select::make('icon')
                                            ->label('Icon')
                                            ->options([
                                                'clock'           => 'Đồng hồ',
                                                'check-circle'    => 'Hoàn thành',
                                                'users'           => 'Nhóm người',
                                                'briefcase'       => 'Cặp táp',
                                                'building-office' => 'Tòa nhà',
                                                'globe-alt'       => 'Toàn cầu',
                                                'trophy'          => 'Cúp',
                                                'star'            => 'Ngôi sao',
                                                'heart'           => 'Trái tim',
                                                'rocket-launch'   => 'Tên lửa',
                                            ])
                                            ->required(),
                                        TextInput::make('value')
                                            ->label('Số liệu')
                                            ->required(),
                                        TextInput::make('label')
                                            ->label('Nhãn hiển thị')
                                            ->required(),
                                        Select::make('color')
                                            ->label('Màu sắc')
                                            ->options([
                                                'blue'    => 'Xanh dương',
                                                'emerald' => 'Xanh lá',
                                                'amber'   => 'Vàng',
                                                'violet'  => 'Tím',
                                                'rose'    => 'Đỏ hồng',
                                                'cyan'    => 'Xanh ngọc',
                                                'orange'  => 'Cam',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(4)
                                    ->reorderable()
                                    ->collapsible()
                                    ->defaultItems(4)
                                    ->maxItems(8)
                                    ->columnSpanFull(),
                            ]),

                        // ═══════════════════════════════════════
                        // TAB 3: TIÊU ĐỀ CÁC KHỐI
                        // ═══════════════════════════════════════
                        Tab::make('Tiêu đề các khối')
                            ->icon('heroicon-o-rectangle-group')
                            ->schema([
                                Section::make('Khối Dịch vụ')
                                    ->schema([
                                        TextInput::make('services_title')
                                            ->label('Tiêu đề'),
                                        Textarea::make('services_description')
                                            ->label('Mô tả')
                                            ->rows(2),
                                    ])->columns(2),
                                Section::make('Khối Lĩnh vực')
                                    ->schema([
                                        TextInput::make('fields_title')
                                            ->label('Tiêu đề'),
                                        Textarea::make('fields_description')
                                            ->label('Mô tả')
                                            ->rows(2),
                                    ])->columns(2),
                                Section::make('Khối Dự án')
                                    ->schema([
                                        TextInput::make('projects_title')
                                            ->label('Tiêu đề'),
                                        Textarea::make('projects_description')
                                            ->label('Mô tả')
                                            ->rows(2),
                                    ])->columns(2),
                                Section::make('Khối Tour nổi bật')
                                    ->schema([
                                        TextInput::make('products_title')
                                            ->label('Tiêu đề'),
                                        Textarea::make('products_description')
                                            ->label('Mô tả')
                                            ->rows(2),
                                    ])->columns(2),
                                Section::make('Khối Tin tức')
                                    ->schema([
                                        TextInput::make('posts_title')
                                            ->label('Tiêu đề'),
                                        Textarea::make('posts_description')
                                            ->label('Mô tả')
                                            ->rows(2),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
