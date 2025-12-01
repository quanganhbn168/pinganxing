<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class TocHelper
{
    /**
     * Xử lý nội dung bài viết:
     * 1. Gắn ID vào thẻ h2, h3, h4
     * 2. Trả về danh sách Heading để làm mục lục
     * * @return array ['html' => 'Nội dung đã gắn ID', 'toc' => [Danh sách mục lục]]
     */
    public static function process(?string $content): array
    {
        if (empty($content)) {
            return ['html' => '', 'toc' => []];
        }

        $toc = [];
        
        // Regex bắt thẻ h2, h3, h4
        $pattern = '/<h([2-4])(.*?)>(.*?)<\/h\1>/usi';

        $html = preg_replace_callback($pattern, function ($matches) use (&$toc) {
            $level = $matches[1]; // 2, 3, 4
            $attrs = $matches[2]; // class, style...
            $textRaw = $matches[3]; // Nội dung gốc

            // 1. Xử lý text sạch để hiển thị và tạo slug
            $textClean = strip_tags(html_entity_decode($textRaw, ENT_QUOTES, 'UTF-8'));
            
            // 2. Tạo slug (ID)
            $slug = Str::slug($textClean);
            
            // Nếu slug rỗng (do tiêu đề toàn ký tự đặc biệt), tạo random để không lỗi
            if (empty($slug)) {
                $slug = 'heading-' . uniqid();
            }

            // 3. Thêm vào mảng TOC
            $toc[] = [
                'level' => $level,
                'text'  => $textClean,
                'slug'  => $slug
            ];

            // 4. Trả về HTML mới có ID
            return "<h{$level}{$attrs} id=\"{$slug}\">{$textRaw}</h{$level}>";

        }, $content);

        return [
            'html' => $html,
            'toc'  => $toc
        ];
    }
}