<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category; 
class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $categoryId = $this->route('category') ? $this->route('category')->id : null;
        return [
            'parent_id' => ['nullable', 'integer', 'exists:categories,id', Rule::notIn([$categoryId])], 
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId)
            ],
            'status' => 'boolean',
            'is_home' => 'boolean',
            'is_menu' => 'boolean',
            'is_footer' => 'boolean',
            'position' => 'nullable|integer|min:0',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }
    public function attributes(): array
    {
        return [
            'parent_id' => 'danh mục cha',
            'name' => 'tên danh mục',
            'slug' => 'slug',
            'status' => 'trạng thái',
            'is_home' => 'hiển thị trang chủ',
            'is_menu' => 'hiển thị menu',
            'is_footer' => 'hiển thị footer',
            'position' => 'vị trí',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
            'parent_id.not_in' => 'Danh mục cha không thể là chính nó',
            'status.boolean' => 'Trạng thái không hợp lệ',
            'is_home.boolean' => 'Hiển thị trang chủ không hợp lệ',
            'is_menu.boolean' => 'Hiển thị menu không hợp lệ',
            'is_footer.boolean' => 'Hiển thị footer không hợp lệ',
            'position.integer' => 'Vị trí phải là số nguyên',
            'position.min' => 'Vị trí không được nhỏ hơn 0',
            'meta_description.max' => 'Meta description không được vượt quá 500 ký tự',
            'meta_keywords.max' => 'Meta keywords không được vượt quá 255 ký tự',
        ];
    }
    protected function prepareForValidation(): void
    {
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name),
            ]);
        }
        $this->merge([
            'status' => (bool) $this->status,
            'is_home' => (bool) $this->is_home,
            'is_menu' => (bool) $this->is_menu,
            'is_footer' => (bool) $this->is_footer,
            'position' => $this->position ? (int) $this->position : 0,
        ]);
        if ($this->parent_id === null || $this->parent_id === '') {
            $this->merge([
                'parent_id' => null,
            ]);
        }
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /** @var \App\Models\Category|null $current */
            $current = $this->route('category'); 
            $parentId = $this->input('parent_id');
            if (!$current || !$parentId) {
                return;
            }
            if ((int)$parentId === (int)$current->id) {
                $validator->errors()->add('parent_id', 'Danh mục cha không thể là chính nó.');
                return;
            }
            $ancestor = Category::select('id', 'parent_id')->find($parentId);
            while ($ancestor) {
                if ((int)$ancestor->id === (int)$current->id) {
                    $validator->errors()->add(
                        'parent_id',
                        'Danh mục cha không thể là một danh mục con/cháu của chính nó.'
                    );
                    break;
                }
                $ancestor = $ancestor->parent_id
                    ? Category::select('id', 'parent_id')->find($ancestor->parent_id)
                    : null;
            }
        });
    }
}