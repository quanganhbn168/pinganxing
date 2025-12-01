<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'position' => 'nullable|integer|min:0',
            'content'  => 'required|string',
            'status'   => 'sometimes|boolean',
            // media-input trả về path ẩn, không validate file ở đây
            'image_original_path' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên khách hàng',
            'position' => 'thứ tự',
            'content' => 'nội dung',
            'status' => 'trạng thái',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status'   => (bool) $this->boolean('status'),
            'position' => $this->input('position', 0),
        ]);
    }
}
