<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Chuẩn hoá dữ liệu trước khi validate:
     * - value: bỏ ký tự không phải số (từ money-input) → số nguyên
     * - status, is_home: về boolean 0/1
     * - slug: trim, rỗng thì để null
     * - gallery_original_paths: nếu là JSON string thì decode về array
     */
    protected function prepareForValidation(): void
    {
        // Giá trị gói thầu (money-input trả chuỗi số) -> int
        $value = $this->input('value');
        if (is_string($value)) {
            $value = preg_replace('/\D+/', '', $value) ?? null; // chỉ giữ chữ số
        }

        // gallery có thể submit dạng JSON string
        $gallery = $this->input('gallery_original_paths');
        if (is_string($gallery)) {
            $decoded = json_decode($gallery, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $gallery = $decoded;
            }
        }

        // slug
        $slug = trim((string) $this->input('slug', ''));
        $slug = $slug !== '' ? $slug : null;

        $this->merge([
            'value'                  => $value !== '' ? $value : null,
            'status'                 => (int) $this->boolean('status'),
            'is_home'                => (int) $this->boolean('is_home'),
            'slug'                   => $slug,
            'gallery_original_paths' => $gallery,
        ]);
    }

    public function rules(): array
    {
        // Lấy id khi update (route model binding: admin.projects.update)
        $projectId = optional($this->route('project'))->id;

        return [
            // Chính
            'name'                => ['required', 'string', 'max:255'],
            'project_category_id' => ['required', 'integer', 'exists:project_categories,id'],

            // Slug có thể để trống (tự sinh ở service/controller nếu muốn)
            'slug'                => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->ignore($projectId),
            ],

            // Thông tin chi tiết
            'investor'            => ['nullable', 'string', 'max:255'],
            'address'             => ['nullable', 'string', 'max:255'],
            'year'                => ['nullable', 'integer', 'between:1900,2100'],
            'value'               => ['nullable', 'integer', 'min:0'],

            // Nội dung
            'description'         => ['nullable', 'string'],
            'content'             => ['nullable', 'string'],

            // Media (chuẩn MediaService)
            'image_original_path'  => ['nullable', 'string', 'max:1024'],
            'banner_original_path' => ['nullable', 'string', 'max:1024'],
            // Cho phép mảng đường dẫn gallery; nếu để JSON string sẽ được prepareForValidation decode
            'gallery_original_paths'   => ['nullable', 'array'],
            'gallery_original_paths.*' => ['string', 'max:1024'],

            // Cờ
            'status'              => ['required', 'boolean'],
            'is_home'             => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                => 'Tên dự án là bắt buộc.',
            'name.string'                  => 'Tên dự án phải là chuỗi.',
            'name.max'                     => 'Tên dự án không vượt quá 255 ký tự.',

            'project_category_id.required' => 'Vui lòng chọn danh mục dự án.',
            'project_category_id.integer'  => 'Danh mục không hợp lệ.',
            'project_category_id.exists'   => 'Danh mục đã chọn không tồn tại.',

            'slug.string'                  => 'Slug phải là chuỗi.',
            'slug.max'                     => 'Slug không vượt quá 255 ký tự.',
            'slug.unique'                  => 'Slug đã tồn tại trong hệ thống.',

            'investor.string'              => 'Chủ đầu tư phải là chuỗi.',
            'investor.max'                 => 'Chủ đầu tư không vượt quá 255 ký tự.',
            'address.string'               => 'Địa chỉ phải là chuỗi.',
            'address.max'                  => 'Địa chỉ không vượt quá 255 ký tự.',
            'year.integer'                 => 'Năm thực hiện phải là số.',
            'year.between'                 => 'Năm thực hiện phải trong khoảng 1900–2100.',
            'value.integer'                => 'Giá trị gói thầu phải là số.',
            'value.min'                    => 'Giá trị gói thầu không nhỏ hơn 0.',

            'description.string'           => 'Mô tả phải là chuỗi.',
            'content.string'               => 'Nội dung phải là chuỗi.',

            'image_original_path.string'   => 'Đường dẫn ảnh đại diện không hợp lệ.',
            'image_original_path.max'      => 'Đường dẫn ảnh đại diện quá dài.',
            'banner_original_path.string'  => 'Đường dẫn banner không hợp lệ.',
            'banner_original_path.max'     => 'Đường dẫn banner quá dài.',
            'gallery_original_paths.array' => 'Danh sách ảnh gallery không hợp lệ.',
            'gallery_original_paths.*.string' => 'Mỗi đường dẫn ảnh gallery phải là chuỗi.',
            'gallery_original_paths.*.max'    => 'Đường dẫn ảnh gallery quá dài.',

            'status.required'              => 'Trạng thái là bắt buộc.',
            'status.boolean'               => 'Trạng thái không hợp lệ.',
            'is_home.boolean'              => 'Giá trị hiển thị trang chủ không hợp lệ.',
        ];
    }
}
