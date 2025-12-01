<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CareerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'                => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255',
            'salary'              => 'nullable|string|max:255',
            'quantity'            => 'nullable|integer|min:1',
            'education'           => 'nullable|string|max:255',
            'location'            => 'nullable|string|max:255',
            'type'                => 'nullable|string|max:50',
            'deadline'            => 'nullable|date|after_or_equal:today',
            
            'image_original_path' => 'nullable|string', 
            'description'         => 'nullable|string',
            'requirement'         => 'nullable|string',
            'benefit'             => 'nullable|string',
            
            'status'              => 'nullable|boolean',
            'is_home'             => 'nullable|boolean',
            'position'            => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'deadline.after_or_equal' => 'Hạn nộp hồ sơ phải từ ngày hôm nay trở đi.',
        ];
    }
}