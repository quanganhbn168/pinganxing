<?php

namespace App\Enums;

enum SliderType: string
{
    // Các giá trị này sẽ được lưu vào cột 'type' trong database
    case HOME = 'home';                 // Slide trang chủ
    case BANNER_AD = 'banner_ad';       // Quảng cáo
    case SIDEBAR = 'sidebar';           // Cột bên
    case PARTNER = 'partner';           // Đối tác/Logo footer

    // Hàm trả về tên hiển thị tiếng Việt (dùng cho Admin)
    public function label(): string
    {
        return match($this) {
            self::HOME => 'Slide Chính (Home)',
            self::BANNER_AD => 'Banner Quảng Cáo',
            self::SIDEBAR => 'Ảnh Cột Bên',
            self::PARTNER => 'Logo Đối Tác',
        };
    }
    
    // Hàm lấy màu sắc cho badge (tùy chọn - giúp giao diện đẹp hơn)
    public function color(): string
    {
        return match($this) {
            self::HOME => 'primary',   // Màu xanh dương
            self::BANNER_AD => 'danger', // Màu đỏ
            self::SIDEBAR => 'info',   // Màu xanh nhạt
            self::PARTNER => 'secondary', // Màu xám
        };
    }
}