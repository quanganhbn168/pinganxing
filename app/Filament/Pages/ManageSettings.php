<?php

namespace App\Filament\Pages;

use App\Models\Menu;
use App\Settings\GeneralSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use UnitEnum;

class ManageSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Cấu hình';
    protected static ?string $navigationLabel = 'Cài đặt chung';
    protected static ?string $title = 'Cài đặt chung';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        // ═══════════════════════════════════════
                        // TAB 1: THÔNG TIN CƠ BẢN
                        // ═══════════════════════════════════════
                        Tab::make('Thông tin cơ bản')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Tên Website / Công ty')
                                    ->required()
                                    ->columnSpanFull(),
                                CuratorPicker::make('logo')
                                    ->label('Logo thương hiệu')
                                    ->acceptedFileTypes(['image/*']),
                                CuratorPicker::make('favicon')
                                    ->label('Favicon')
                                    ->acceptedFileTypes(['image/*']),
                                CuratorPicker::make('banner')
                                    ->label('Banner trang chủ')
                                    ->acceptedFileTypes(['image/*'])
                                    ->columnSpanFull(),
                                CuratorPicker::make('catalog_file')
                                    ->label('Hồ sơ năng lực (Catalog PDF)')
                                    ->acceptedFileTypes(['application/pdf']),
                                TextInput::make('business_code')
                                    ->label('Mã số đăng ký kinh doanh'),
                                TextInput::make('tax_code')
                                    ->label('Mã số thuế'),
                            ])
                            ->columns(2),

                        // ═══════════════════════════════════════
                        // TAB 2: THÔNG TIN LIÊN HỆ
                        // ═══════════════════════════════════════
                        Tab::make('Thông tin liên hệ')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Số điện thoại (Giá trị thực)')
                                    ->tel()
                                    ->helperText('Số dùng để gọi: VD 0987654321'),
                                TextInput::make('phone_display')
                                    ->label('Số điện thoại (Hiển thị)')
                                    ->helperText('Số hiển thị trên website: VD 098.765.4321 hoặc (024) 3xxx xxxx'),
                                TextInput::make('email')
                                    ->label('Email liên hệ')
                                    ->email(),
                                TextInput::make('working_hours')
                                    ->label('Giờ làm việc')
                                    ->helperText('Hiển thị trên Header gốc ngang màn hình'),
                                TextInput::make('address')
                                    ->label('Địa chỉ công ty')
                                    ->columnSpanFull(),
                                TextInput::make('map')
                                    ->label('Link Google Maps (iframe hoặc URL)')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // ═══════════════════════════════════════
                        // TAB 3: LIÊN KẾT & MXH
                        // ═══════════════════════════════════════
                        Tab::make('Liên kết & MXH')
                            ->icon('heroicon-o-link')
                            ->schema([
                                TextInput::make('bct_link')
                                    ->label('Link Bộ Công Thương')
                                    ->url()
                                    ->helperText('Bỏ trống nếu không có'),
                                TextInput::make('zalo')
                                    ->label('Link Zalo'),
                                TextInput::make('messenger')
                                    ->label('Link Messenger'),
                                TextInput::make('youtube')
                                    ->label('Link Youtube'),
                                TextInput::make('tiktok')
                                    ->label('Link Tiktok'),
                            ])
                            ->columns(2),

                        // ═══════════════════════════════════════
                        // TAB 4: CẤU HÌNH MENU
                        // ═══════════════════════════════════════
                        Tab::make('Cấu hình Menu')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Section::make('Menu Header')
                                    ->description('Chọn menu hiển thị trên thanh điều hướng chính.')
                                    ->schema([
                                        Select::make('header_menu_id')
                                            ->label('Menu Header')
                                            ->options(fn () => Menu::where('location', 'header')->where('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Chọn menu chính hiển thị trên header website.'),
                                    ]),

                                Section::make('Menu Footer')
                                    ->description('Chọn menu và tiêu đề cho các cột trong footer.')
                                    ->schema([
                                        CuratorPicker::make('footer_background')
                                            ->label('Hình nền Footer')
                                            ->acceptedFileTypes(['image/*'])
                                            ->columnSpanFull(),
                                        TextInput::make('footer_col_2_title')
                                            ->label('Tiêu đề Cột 2'),
                                        Select::make('footer_col_2_menu_id')
                                            ->label('Menu Cột 2')
                                            ->options(fn () => Menu::where('location', 'footer')->where('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Chọn menu footer để hiển thị'),
                                        TextInput::make('footer_col_3_title')
                                            ->label('Tiêu đề Cột 3'),
                                        Select::make('footer_col_3_menu_id')
                                            ->label('Menu Cột 3')
                                            ->options(fn () => Menu::where('location', 'footer')->where('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Chọn menu footer để hiển thị'),
                                    ])
                                    ->columns(2),
                            ]),

                        // ═══════════════════════════════════════
                        // TAB 5: GIỚI THIỆU TRANG CHỦ
                        // ═══════════════════════════════════════
                        Tab::make('Giới thiệu trang chủ')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Nội dung giới thiệu')
                                    ->schema([
                                        TextInput::make('intro_title')
                                            ->label('Tiêu đề')
                                            ->columnSpanFull(),
                                        Textarea::make('intro_description')
                                            ->label('Mô tả ngắn')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        CuratorPicker::make('intro_image')
                                            ->label('Hình ảnh minh họa')
                                            ->acceptedFileTypes(['image/*'])
                                            ->columnSpanFull(),
                                    ])->columns(1),

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

                                Section::make('Tiêu đề & Mô tả các khối khác')
                                    ->schema([
                                        TextInput::make('services_title')
                                            ->label('Tiêu đề: Dịch vụ cung cấp'),
                                        Textarea::make('services_description')
                                            ->label('Mô tả khối: Dịch vụ cung cấp')
                                            ->rows(2),
                                        TextInput::make('fields_title')
                                            ->label('Tiêu đề: Lĩnh vực hoạt động'),
                                        Textarea::make('fields_description')
                                            ->label('Mô tả khối: Lĩnh vực hoạt động')
                                            ->rows(2),
                                        TextInput::make('projects_title')
                                            ->label('Tiêu đề: Dự án tiêu biểu'),
                                        Textarea::make('projects_description')
                                            ->label('Mô tả khối: Dự án tiêu biểu')
                                            ->rows(2),
                                    ])->columns(2),
                            ]),


                        // ═══════════════════════════════════════
                        // TAB 7: COUNTER THỐNG KÊ
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
                                                'clock' => 'Đồng hồ',
                                                'check-circle' => 'Hoàn thành',
                                                'users' => 'Nhóm người',
                                                'briefcase' => 'Cặp táp',
                                                'building-office' => 'Tòa nhà',
                                                'globe-alt' => 'Toàn cầu',
                                                'trophy' => 'Cúp',
                                                'star' => 'Ngôi sao',
                                                'heart' => 'Trái tim',
                                                'rocket-launch' => 'Tên lửa',
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
                                                'blue' => 'Xanh dương',
                                                'emerald' => 'Xanh lá',
                                                'amber' => 'Vàng',
                                                'violet' => 'Tím',
                                                'rose' => 'Đỏ hồng',
                                                'cyan' => 'Xanh ngọc',
                                                'orange' => 'Cam',
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
                        // TAB 8: SEO & SCRIPT
                        // ═══════════════════════════════════════
                        Tab::make('SEO & Script')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('SEO')
                                    ->schema([
                                        Textarea::make('meta_description')
                                            ->label('Mô tả trang (Meta Description)')
                                            ->rows(3)
                                            ->helperText('Hiển thị dưới tiêu đề trên Google, tối đa 160 ký tự'),
                                        Textarea::make('meta_keywords')
                                            ->label('Từ khóa (Meta Keywords)')
                                            ->rows(2),
                                        CuratorPicker::make('meta_image')
                                            ->label('Ảnh chia sẻ mặc định (OG Image)')
                                            ->acceptedFileTypes(['image/*'])
                                            ->helperText('Ảnh hiển thị khi chia sẻ link trên Facebook, Zalo, Telegram... (khuyến nghị 1200x630px)'),
                                    ])
                                    ->columns(1),
                                Section::make('Mã nhúng (Script)')
                                    ->schema([
                                        Textarea::make('head_script')
                                            ->label('Mã chèn trong <head>')
                                            ->rows(4)
                                            ->helperText('Google Analytics, Facebook Pixel, v.v.'),
                                        Textarea::make('body_start_script')
                                            ->label('Mã chèn đầu <body>')
                                            ->rows(4)
                                            ->helperText('Google Tag Manager noscript, Schema.org, v.v.'),
                                        Textarea::make('body_script')
                                            ->label('Mã chèn cuối <body>')
                                            ->rows(4)
                                            ->helperText('Chat widget, Tawk.to, Zalo OA, v.v.'),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
