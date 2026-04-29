<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tag = $this->route('tag');
        $id = is_object($tag) && method_exists($tag, 'getKey') ? $tag->getKey() : $tag;

        return [
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }
}
