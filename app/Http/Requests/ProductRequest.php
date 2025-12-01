<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Không còn chuẩn bị dữ liệu biến thể
    protected function prepareForValidation()
    {
        // noop
    }

    public function rules(): array
    {
        $routeParam = $this->route('product');
        $productId  = is_object($routeParam) ? ($routeParam->id ?? null) : ($routeParam ?? null);

        return [
            // Cơ bản
            'name'        => ['required', 'string', 'max:255'],
            'code'        => ['required', 'string', 'max:255', Rule::unique('products', 'code')->ignore($productId)],
            'brand_id'    => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status'      => ['required', 'boolean'],
            'is_home'     => ['nullable', 'boolean'],

            // Nội dung
            'description' => ['nullable', 'string'],
            'content'     => ['nullable', 'string'],
            'specifications'     => ['nullable', 'string'],

            // Media (dùng Media Picker)
            'image_original_path'    => ['nullable', 'string'], // path tương đối trong disk
            'gallery_original_paths' => ['nullable', 'string'], // JSON string (mảng path)

            // (Tuỳ dự án) Giá/tồn kho nếu có trong form — không liên quan biến thể
            'price_discount'   => ['nullable', 'numeric', 'min:0'],
            'price'            => ['nullable', 'numeric', 'min:0', 'gte:price_discount'],
            'stock_quantity'   => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Tên sản phẩm là bắt buộc.',
            'name.string'          => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max'             => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'code.required'        => 'Mã sản phẩm là bắt buộc.',
            'code.string'          => 'Mã sản phẩm phải là chuỗi ký tự.',
            'code.max'             => 'Mã sản phẩm không được vượt quá 255 ký tự.',
            'code.unique'          => 'Mã sản phẩm đã tồn tại trong hệ thống.',

            'brand_id.integer'     => 'Thương hiệu không hợp lệ.',
            'brand_id.exists'      => 'Thương hiệu đã chọn không tồn tại.',

            'category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'category_id.integer'  => 'Danh mục không hợp lệ.',
            'category_id.exists'   => 'Danh mục đã chọn không tồn tại.',

            'status.required'      => 'Trạng thái là bắt buộc.',
            'status.boolean'       => 'Trạng thái không hợp lệ.',

            'description.string'   => 'Mô tả phải là chuỗi.',
            'content.string'       => 'Nội dung phải là chuỗi.',
            'specifications.string'       => 'Nội dung phải là chuỗi.',

            // Media
            'image_original_path.string'     => 'Đường dẫn ảnh đại diện không hợp lệ.',
            'gallery_original_paths.string'  => 'Dữ liệu gallery phải là chuỗi JSON hợp lệ.',

            // Giá/tồn kho (nếu dùng)
            'price_discount.numeric' => 'Giá bán phải là số.',
            'price_discount.min'     => 'Giá bán không được nhỏ hơn 0.',
            'price.numeric'          => 'Giá so sánh phải là số.',
            'price.min'              => 'Giá so sánh không được nhỏ hơn 0.',
            'price.gte'              => 'Giá so sánh phải lớn hơn hoặc bằng giá bán.',
            'stock_quantity.integer' => 'Tồn kho phải là số nguyên.',
            'stock_quantity.min'     => 'Tồn kho không được nhỏ hơn 0.',
        ];
    }
}
