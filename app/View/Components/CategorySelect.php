<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CategorySelect extends Component
{
    public $options = [];
    public $selected;
    public $name;
    public $id;
    public $placeholder;
    public $disableParents;
    public $ignoreId; // <--- 1. Thêm biến này (ID cần ẩn đi)

    public function __construct(
        $categories, 
        $selected = null, 
        $name = 'category_id', 
        $placeholder = '-- Chọn danh mục --', 
        $id = null,
        $disableParents = true,
        $ignoreId = null // <--- 2. Thêm vào constructor
    )
    {
        $this->name = $name;
        $this->selected = $selected;
        $this->placeholder = $placeholder;
        $this->id = $id ?: $name;
        $this->disableParents = $disableParents;
        $this->ignoreId = $ignoreId;

        $this->options = $this->buildTree($categories);
    }

    private function buildTree($categories, $parentId = null, $prefix = '')
    {
        $result = [];
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $category) {
            // 3. LOGIC QUAN TRỌNG: 
            // Nếu ID của item đang duyệt == ID cần ignore -> Bỏ qua luôn (Cắt nhánh)
            if ($this->ignoreId && $category->id == $this->ignoreId) {
                continue;
            }

            $hasChild = $categories->where('parent_id', $category->id)->isNotEmpty();
            $isDisabled = $hasChild && $this->disableParents;

            $result[] = (object) [
                'id' => $category->id,
                'name' => $prefix . $category->name,
                'disabled' => $isDisabled,
                'style' => $hasChild 
                    ? ($isDisabled ? 'font-weight: 700; background-color: #f2f2f2; color: #999;' : 'font-weight: 700; color: #000;') 
                    : 'padding-left: 20px;'
            ];

            if ($hasChild) {
                $result = array_merge($result, $this->buildTree($categories, $category->id, $prefix . '-- '));
            }
        }
        return $result;
    }

    public function render()
    {
        return view('components.category-select');
    }
}