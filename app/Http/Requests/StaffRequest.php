<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;

        $rules = [
            'name'   => 'required|string|min:2|max:255',
            'phone'  => ['required', 'numeric', Rule::unique('admins', 'phone')->ignore($staffId)],
            'email'  => ['required', 'email', Rule::unique('admins', 'email')->ignore($staffId)],
            'role'   => 'required|string|exists:roles,name',
            'status' => 'boolean',
        ];

        // Password: bắt buộc khi tạo mới, tùy chọn khi sửa
        if ($staffId) {
            $rules['password'] = 'nullable|min:6';
        } else {
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Vui lòng nhập họ tên.',
            'name.min'        => 'Họ tên phải từ 2 ký tự.',
            'phone.required'  => 'Vui lòng nhập số điện thoại.',
            'phone.numeric'   => 'Số điện thoại phải là số.',
            'phone.unique'    => 'Số điện thoại này đã được sử dụng.',
            'email.required'  => 'Vui lòng nhập email.',
            'email.email'     => 'Email không hợp lệ.',
            'email.unique'    => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'    => 'Mật khẩu phải từ 6 ký tự.',
            'role.required'   => 'Vui lòng chọn vai trò.',
            'role.exists'     => 'Vai trò không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'họ tên',
            'phone'    => 'số điện thoại',
            'email'    => 'email',
            'password' => 'mật khẩu',
            'role'     => 'vai trò',
            'status'   => 'trạng thái',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->boolean('status'),
        ]);
    }
}
