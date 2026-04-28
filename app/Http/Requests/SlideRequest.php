<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => 'nullable|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'link'             => 'nullable|string|max:500',
            'button_text'      => 'nullable|string|max:255',
            'position'         => 'nullable|integer|min:0',
            'status'           => 'boolean',
            'image_id'         => 'required|integer',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề slide',
            'link' => 'đường link',
            'position' => 'thứ tự',
            'status' => 'trạng thái hiển thị',
            'image_id' => 'ảnh slide',
        ];
    }

    public function messages(): array
    {
        return [
            'image_id.required' => 'Vui lòng chọn ảnh slide.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => (bool) $this->status,
        ]);
    }
}
