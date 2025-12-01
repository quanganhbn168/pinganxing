<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Collection;
class PostCategorySelect extends Component
{
    public string $name;
    public string $label;
    public $selected;
    public bool $required;
    public array $flatCategories = []; // Mảng phẳng chứa danh mục để hiển thị

    public function __construct(
        string $name,
        string $label,
        Collection $categories,
        $selected = null,
        bool $required = false
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->selected = old($name, $selected);
        $this->required = $required;
        
        // **Logic chính:** Biến cây thư mục thành mảng phẳng
        $this->flatCategories = $this->buildFlatCategoryList($categories);
    }

    /**
     * Hàm đệ quy để tạo danh sách phẳng có thụt lề.
     */
    private function buildFlatCategoryList(Collection $categories, int $level = 0): array
    {
        $result = [];
        $prefix = str_repeat('-- ', $level); // Thêm '-- ' cho mỗi cấp

        foreach ($categories as $category) {
            $result[] = [
                'id' => $category->id,
                'name' => $prefix . $category->name,
            ];

            if ($category->children && $category->children->isNotEmpty()) {
                $result = array_merge($result, $this->buildFlatCategoryList($category->children, $level + 1));
            }
        }

        return $result;
    }

    public function render(): View
    {
        return view('components.form.post-category-select');
    }
}
