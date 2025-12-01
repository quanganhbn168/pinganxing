<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $fieldId = $this->route('field') ? $this->route('field')->id : null;

        return [
            'field_category_id' => 'required|exists:field_categories,id',
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fields', 'slug')->ignore($fieldId)
            ],
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'field_category_id' => 'danh mục lĩnh vực',
            'name' => 'tên lĩnh vực',
            'slug' => 'slug',
            'summary' => 'tóm tắt',
            'content' => 'nội dung',
            'status' => 'trạng thái',
            'is_featured' => 'nổi bật',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'field_category_id.required' => 'Vui lòng chọn danh mục lĩnh vực',
            'field_category_id.exists' => 'Danh mục lĩnh vực không tồn tại',
            'name.required' => 'Vui lòng nhập tên lĩnh vực',
            'name.max' => 'Tên lĩnh vực không được vượt quá 255 ký tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'meta_title.max' => 'Meta title không được vượt quá 255 ký tự',
            'meta_description.max' => 'Meta description không được vượt quá 500 ký tự',
            'meta_keywords.max' => 'Meta keywords không được vượt quá 255 ký tự',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Tự động tạo slug từ name nếu slug trống
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name),
            ]);
        }

        // Đảm bảo status và is_featured là boolean
        $this->merge([
            'status' => (bool) $this->status,
            'is_featured' => (bool) $this->is_featured,
        ]);
    }
}