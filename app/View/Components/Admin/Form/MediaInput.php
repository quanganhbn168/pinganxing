<?php

namespace App\View\Components\Admin\Form;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\View\Component;
use Illuminate\Support\Str; // Đừng quên import Str

class MediaInput extends Component
{
    // Các biến public này sẽ tự động được truyền sang file Blade
    public string $inputId;
    public string $previewId;
    public ?string $finalValue;

    /**
     * Khởi tạo component.
     * Tên các tham số phải TRÙNG KHỚP với props anh truyền vào.
     */
    public function __construct(
        public string $name,
        public ?string $label = null,
        public bool $multiple = false,
        public bool $required = false,
        public ?string $help = null,
        public mixed $value = null,
        public ?string $id = null // Thêm 'id' để bắt $attributes->get('id')
    ) {
        // === LOGIC TỪ @php ĐÃ CHUYỂN VÀO ĐÂY ===

        // 1. Tạo ID
        $this->inputId = $id ?? 'mi_'.Str::slug($name, '_').'_'.uniqid();
        $this->previewId = $this->inputId.'_preview';

        // 2. Xử lý giá trị
        $finalValue = old($name, $this->value); 

        if ($this->multiple) {
            if ($finalValue instanceof Arrayable) {
                // Case 1: Tải trang Edit - $finalValue là một Collection (từ pluck)
                $finalValue = json_encode($finalValue->toArray());
            } else if (is_array($finalValue)) {
                // Case 2: Tải trang Edit - $finalValue là một mảng
                $finalValue = json_encode($finalValue);
            }
            // Case 3: Lỗi Validation - $finalValue đã LÀ 1 JSON string (từ old())
            // Case 4: Mới/Rỗng - $finalValue là null
        }
        
        $this->finalValue = (string) $finalValue;
    }

    /**
     * Lấy view / nội dung component.
     */
    public function render()
    {
        return view('components.admin.form.media-input');
    }
}