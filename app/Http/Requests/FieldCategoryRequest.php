<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('field_category') ? $this->route('field_category')->id : null;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('field_categories', 'slug')->ignore($categoryId)
            ],
            'parent_id' => [
                'required',
                'integer',
                'min:0',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ((int) $value === 0) {
                        return;
                    }

                    if (! \App\Models\FieldCategory::whereKey((int) $value)->exists()) {
                        $fail('Danh mục cha không tồn tại');
                    }
                },
            ],
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'solution_overview' => 'nullable|string',
            'business_challenges' => 'nullable|array',
            'cnetpos_solutions' => 'nullable|array',
            'key_features' => 'nullable|array',
            'impact_stats' => 'nullable|array',
            'implementation_steps' => 'nullable|array',
            'related_project_ids' => 'nullable|array',
            'related_project_ids.*' => 'integer|exists:projects,id',
            'is_home' => 'boolean',
            'status' => 'boolean',
            'order' => 'nullable|integer|min:0',
            // Media
            'image_original_path'  => ['nullable', 'max:1024'],
            'banner_original_path' => ['nullable', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên danh mục',
            'slug' => 'slug',
            'parent_id' => 'danh mục cha',
            'description' => 'mô tả',
            'content' => 'nội dung',
            'solution_overview' => 'tổng quan giải pháp',
            'business_challenges' => 'thách thức doanh nghiệp',
            'cnetpos_solutions' => 'giải pháp của CNETPOS',
            'key_features' => 'tính năng nổi bật',
            'impact_stats' => 'hiệu quả đạt được',
            'implementation_steps' => 'quy trình triển khai',
            'related_project_ids' => 'dự án nổi bật liên quan',
            'is_home' => 'lĩnh vực tiêu biểu',
            'status' => 'trạng thái',
            'order' => 'thứ tự',
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
            'order' => $this->order ? (int) $this->order : 0,
            'parent_id' => (int) ($this->parent_id ?? 0),
        ]);
    }
}
