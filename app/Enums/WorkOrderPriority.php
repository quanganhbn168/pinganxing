<?php

namespace App\Enums;

enum WorkOrderPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::LOW => 'Thấp',
            self::MEDIUM => 'Bình thường',
            self::HIGH => 'Cao',
            self::URGENT => 'Khẩn cấp',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'secondary',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::LOW => 'fas fa-arrow-down',
            self::MEDIUM => 'fas fa-minus',
            self::HIGH => 'fas fa-arrow-up',
            self::URGENT => 'fas fa-fire',
        };
    }
}
