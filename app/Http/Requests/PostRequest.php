<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Ép kiểu & mặc định cho checkbox khi không gửi lên.
     * (form unchecked sẽ không có key -> set về 0)
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => (int) ($this->boolean('is_featured')),
            'status'      => (int) ($this->boolean('status')),
        ]);
    }

    public function rules(): array
    {
        // Nếu dùng route model binding: route('post') sẽ trả về model hoặc id
        $postId = optional($this->route('post'))->id ?? $this->route('post');

        return [
            'post_category_id' => ['required', 'integer', 'exists:post_categories,id'],
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'image'            => ['nullable', 'string',],
            'banner'           => ['nullable', 'string',],
            'description'      => ['nullable', 'string'],
            'content'          => ['nullable', 'string'],
            'is_featured'      => ['sometimes', 'in:0,1'],
            'status'           => ['sometimes', 'in:0,1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'post_category_id' => 'danh mục',
            'title'            => 'tiêu đề',
            'slug'             => 'đường dẫn',
            'image'            => 'ảnh đại diện',
            'banner'           => 'ảnh banner',
            'description'      => 'mô tả ngắn',
            'content'          => 'nội dung',
            'is_featured'      => 'nổi bật',
            'status'           => 'trạng thái',
        ];
    }

    public function messages(): array
    {
        return [
            'post_category_id.required' => 'Vui lòng chọn :attribute.',
            'post_category_id.exists'   => ':attribute không hợp lệ.',
            'title.required'            => 'Vui lòng nhập :attribute.',
            'title.max'                 => ':attribute tối đa :max ký tự.',
            'slug.max'                  => ':attribute tối đa :max ký tự.',
            'slug.unique'               => ':attribute đã tồn tại, vui lòng chọn giá trị khác.',
            'is_featured.in'            => ':attribute không hợp lệ.',
            'status.in'                 => ':attribute không hợp lệ.',
        ];
    }
}
