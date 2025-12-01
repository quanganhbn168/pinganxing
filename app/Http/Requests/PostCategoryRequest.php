<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\PostCategory; 

class PostCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('post_category') ? $this->route('post_category')->id : null;

        return [
            
            'parent_id' => ['nullable', 'integer', 'exists:post_categories,id', Rule::notIn([$categoryId])],
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('post_categories', 'slug')->ignore($categoryId)
            ],
            'status' => 'boolean',
            'is_home' => 'boolean',
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
            /** @var \App\Models\PostCategory|null $current */
            $current = $this->route('post_category'); 
            $parentId = $this->input('parent_id');

            
            if (!$current || !$parentId) {
                return;
            }

            
            if ((int)$parentId === (int)$current->id) {
                $validator->errors()->add('parent_id', 'Danh mục cha không thể là chính nó.');
                return;
            }

            
            $ancestor = PostCategory::select('id', 'parent_id')->find($parentId);
            while ($ancestor) {
                if ((int)$ancestor->id === (int)$current->id) {
                    $validator->errors()->add(
                        'parent_id',
                        'Danh mục cha không thể là một danh mục con/cháu của chính nó.'
                    );
                    break;
                }
                $ancestor = $ancestor->parent_id
                    ? PostCategory::select('id', 'parent_id')->find($ancestor->parent_id)
                    : null;
            }
        });
    }
}
