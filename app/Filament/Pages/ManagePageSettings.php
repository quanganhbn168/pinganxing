<?php

namespace App\Filament\Pages;

use App\Settings\PageSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Pages\SettingsPage;
use UnitEnum;

class ManagePageSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Cấu hình';
    protected static ?string $navigationLabel = 'Cài đặt trang';
    protected static ?string $title = 'Cài đặt trang danh mục';

    protected static string $settings = PageSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('PageSettings')
                    ->tabs([
                        $this->pageTab('Sản phẩm', 'heroicon-o-cube', 'products'),
                        $this->pageTab('Dự án', 'heroicon-o-building-office', 'projects'),
                        $this->pageTab('Dịch vụ', 'heroicon-o-wrench-screwdriver', 'services'),
                        $this->pageTab('Lĩnh vực', 'heroicon-o-squares-2x2', 'fields'),
                        $this->pageTab('Tin tức', 'heroicon-o-newspaper', 'posts'),
                        $this->pageTab('Giới thiệu', 'heroicon-o-information-circle', 'intro'),
                        $this->pageTab('Tuyển dụng', 'heroicon-o-briefcase', 'careers'),
                        $this->pageTab('Liên hệ', 'heroicon-o-phone', 'contact'),
                    ])
                    ->columnSpanFull()
            ]);
    }

    private function pageTab(string $label, string $icon, string $prefix): Tab
    {
        return Tab::make($label)
            ->icon($icon)
            ->schema([
                TextInput::make("{$prefix}_title")
                    ->label('Tiêu đề trang')
                    ->required(),
                TextInput::make("{$prefix}_headline")
                    ->label('Headline / Slogan')
                    ->helperText('Dòng phụ đề hiển thị dưới tiêu đề trên banner'),
                Textarea::make("{$prefix}_description")
                    ->label('Mô tả SEO')
                    ->rows(2)
                    ->helperText('Mô tả ngắn hiển thị trên Google'),
                CuratorPicker::make("{$prefix}_banner")
                    ->label('Banner trang')
                    ->acceptedFileTypes(['image/*']),
                RichEditor::make("{$prefix}_content")
                    ->label('Nội dung trang')
                    ->columnSpanFull()
                    ->json(false)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('page-content'),
            ]);
    }
}
