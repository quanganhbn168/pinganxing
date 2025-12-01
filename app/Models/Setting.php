<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    // Cấu hình khóa chính (giữ nguyên theo code cũ của bạn)
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'logo',
        'banner',
        'favicon',
        'email',
        'phone',
        'address',
        'map',
        
        // --- Mạng xã hội ---
        'zalo',
        'mess',
        'tiktok',
        'youtube',

        // --- [MỚI] Profile & Video ---
        'profile',          // Đường dẫn file PDF hồ sơ năng lực
        'video_type',       // Loại video: 'youtube' hoặc 'upload'
        'intro_video',      // Đường dẫn file Video (nếu upload)
        'intro_video_url',  // Link Youtube (nếu chọn youtube)

        // --- SEO & Scripts ---
        'meta_description',
        'meta_keywords',
        'meta_image',
        'schema_script',
        'head_script',
        'body_script',
    ];
}