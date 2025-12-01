<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\SliderType;

class SlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'link'             => 'nullable|string|max:500',
            'position'         => 'nullable|integer|min:0',
            'status'           => 'boolean',
            'image_original_path' => 'required|string|max:255',
            'type'  => ['required', new Enum(SliderType::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề slide',
            'link' => 'đường link',
            'position' => 'thứ tự',
            'status' => 'trạng thái hiển thị',
            'image_original_path' => 'ảnh slide',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề slide.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => (bool) $this->status,
        ]);
    }
}
