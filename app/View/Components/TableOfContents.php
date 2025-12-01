<?php

namespace App\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class TableOfContents extends Component
{
    /**
     * Mảng chứa các tiêu đề đã được xử lý.
     * @var array
     */
    public array $headings = [];

    /**
     * Tạo một instance component mới.
     *
     * @param string $content Nội dung HTML của bài viết.
     */
    public function __construct(string $content = '')
    {
        // Biểu thức chính quy để tìm tất cả các thẻ <h2>
        preg_match_all('/<h2.*?>(.*?)<\/h2>/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $title) {
                $this->headings[] = [
                    'text' => strip_tags($title), // Lấy text thuần, loại bỏ các thẻ HTML khác có thể có bên trong
                    'slug' => Str::slug(strip_tags($title)), // Tạo một slug duy nhất để làm anchor link
                ];
            }
        }
    }

    /**
     * Lấy view / nội dung đại diện cho component.
     */
    public function render(): View
    {
        return view('components.table-of-contents');
    }
}