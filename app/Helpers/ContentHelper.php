<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class ContentHelper
{
    public static function addIdsToHeadings(?string $content): string
    {
        if (is_null($content)) {
            return '';
        }

        // Regex bắt cả h2, h3, h4 (case-insensitive 'i')
        // <h([2-4]) : Bắt thẻ h2, h3 hoặc h4, lưu số vào nhóm 1
        // (.*?)     : Bắt các thuộc tính (class, style...) vào nhóm 2
        // >(.*?)    : Bắt nội dung tiêu đề vào nhóm 3
        // <\/h\1>   : Bắt thẻ đóng tương ứng (nếu mở h2 thì phải đóng h2)
        $pattern = '/<h([2-4])(.*?)>(.*?)<\/h\1>/usi';

        return preg_replace_callback($pattern, function ($matches) {
            $level = $matches[1]; // 2, 3 hoặc 4
            $attrs = $matches[2]; // class="...", style="..."
            $originalTitle = $matches[3]; // Nội dung tiêu đề

            // Làm sạch tiêu đề để tạo slug
            $cleanTitle = html_entity_decode($originalTitle, ENT_QUOTES, 'UTF-8');
            $slug = Str::slug(strip_tags($cleanTitle));

            // Trả về thẻ với ID mới, giữ nguyên level (h2, h3...) và attributes cũ
            return "<h{$level}{$attrs} id=\"{$slug}\">{$originalTitle}</h{$level}>";
        }, $content);
    }
}