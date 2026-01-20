<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        $rules = [
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'map'              => 'nullable|string',
            'schema_script'    => 'nullable|string',
            'head_script'      => 'nullable|string',
            'body_script'      => 'nullable|string',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'zalo'             => 'nullable|url',
            'mess'             => 'nullable|url',
            'tiktok'           => 'nullable|url',
            'youtube'          => 'nullable|url',
            'favicon'          => 'nullable|image|mimes:ico,png,jpg,jpeg|max:512',
            'video_type'      => 'nullable|string|in:youtube,upload',
            'intro_video_url' => 'nullable|url',
        ];

        // Logo
        if ($this->hasFile('logo')) {
            $rules['logo'] = 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048';
        } else {
            $rules['logo'] = 'nullable|string';
        }

        // Banner
        if ($this->hasFile('banner')) {
            $rules['banner'] = 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048';
        } else {
            $rules['banner'] = 'nullable|string';
        }

        // Meta Image
        if ($this->hasFile('meta_image')) {
            $rules['meta_image'] = 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048';
        } else {
            $rules['meta_image'] = 'nullable|string';
        }

        // Profile (PDF)
        if ($this->hasFile('profile')) {
            $rules['profile'] = 'nullable|file|mimes:pdf|max:10240';
        } else {
            $rules['profile'] = 'nullable|string';
        }

        // Intro Video
        if ($this->hasFile('intro_video')) {
            $rules['intro_video'] = 'nullable|file|mimes:mp4,mov,avi|max:51200';
        } else {
            $rules['intro_video'] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'             => 'Tên trang web là bắt buộc.',
            'name.string'               => 'Tên trang web phải là chuỗi ký tự.',
            'name.max'                  => 'Tên trang web không được vượt quá :max ký tự.',

            'email.email'               => 'Email không đúng định dạng.',

            'phone.string'              => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max'                 => 'Số điện thoại không được vượt quá :max ký tự.',

            'address.string'            => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max'               => 'Địa chỉ không được vượt quá :max ký tự.',

            'logo.image'                => 'Logo phải là tệp hình ảnh.',
            'logo.mimes'                => 'Logo phải có định dạng: jpg, jpeg, png, webp, gif.',
            'logo.max'                  => 'Kích thước logo không được vượt quá :max KB.',
            'banner.mimes'              => 'banner phải có định dạng: jpg, jpeg, png, webp, gif.',
            'banner.max'                => 'Kích thước banner không được vượt quá :max KB.',

            'meta_image.image'          => 'Ảnh chia sẻ phải là tệp hình ảnh.',
            'meta_image.mimes'          => 'Ảnh chia sẻ phải có định dạng: jpg, jpeg, png, webp, gif.',
            'meta_image.max'            => 'Kích thước ảnh chia sẻ không được vượt quá :max KB.',

            'meta_description.string'   => 'Mô tả meta phải là chuỗi ký tự.',
            'meta_description.max'      => 'Mô tả meta không được vượt quá :max ký tự.',

            'meta_keywords.string'      => 'Từ khóa meta phải là chuỗi ký tự.',
            'meta_keywords.max'         => 'Từ khóa meta không được vượt quá :max ký tự.',

            'zalo.url'                  => 'Link Zalo không đúng định dạng URL.',
            'mess.url'                  => 'Link Messenger không đúng định dạng URL.',
            'tiktok.url'                => 'Link TikTok không đúng định dạng URL.',
            'youtube.url'               => 'Link Youtube không đúng định dạng URL.',

            'favicon.image'             => 'Favicon phải là tệp hình ảnh.',
            'favicon.mimes'             => 'Favicon phải có định dạng: ico, png, jpg, jpeg.',
            'favicon.max'               => 'Kích thước favicon không được vượt quá :max KB.',
        ];
    }
}
