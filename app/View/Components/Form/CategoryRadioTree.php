<?php

namespace App\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryRadioTree extends Component
{
    public $name;
    public $label;
    public $categories;
    public $selected;
    public $required;

    /**
     * Create a new component instance.
     *
     * @param string $name Tên của input (ví dụ: 'post_category_id')
     * @param string $label Nhãn hiển thị cho form group
     * @param \Illuminate\Database\Eloquent\Collection $categories Collection các danh mục
     * @param mixed $selected ID của danh mục đã được chọn (dùng cho form edit)
     * @param bool $required Có bắt buộc chọn hay không
     */
    public function __construct($name, $label, $categories, $selected = null, $required = false)
    {
        $this->name = $name;
        $this->label = $label;
        $this->categories = $categories; // Giả định đây là collection các danh mục cấp cao nhất
        $this->selected = old($name, $selected); // Lấy giá trị cũ nếu có lỗi validation
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.form.category-radio-tree');
    }
}