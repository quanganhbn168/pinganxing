<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * [THÊM MỚI] Chuẩn bị dữ liệu trước khi validate.
     * Tự động tạo mảng 'variant_attribute_ids' từ dữ liệu của 'variants'.
     */
    protected function prepareForValidation()
    {
        if ($this->boolean('has_variants') && $this->input('variants')) {
            $attributeIds = [];
            foreach ($this->input('variants') as $key => $variantData) {
                $pairs = explode('|', $key);
                foreach ($pairs as $pair) {
                    $parts = explode(':', $pair);
                    if (isset($parts[0]) && is_numeric($parts[0])) {
                        $attributeIds[] = (int)$parts[0];
                    }
                }
            }
            $this->merge([
                'variant_attribute_ids' => array_values(array_unique($attributeIds)),
            ]);
        }
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id ?? null;
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('products', 'code')->ignore($productId)],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image' => [$productId ? 'nullable' : 'required'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            
            'price_discount' => ['required_if:has_variants,false', 'nullable', 'numeric', 'min:0'],
            'price' => ['required_if:has_variants,false', 'nullable', 'numeric', 'min:0', 'gte:price_discount'],
            'has_variants' => ['nullable', 'boolean'],
        ];

        if ($this->boolean('has_variants')) {
            $rules['variants'] = ['required', 'array', 'min:1'];
            
            
            foreach ($this->input('variants', []) as $key => $variant) {
                $rules["variants.{$key}.price"] = ['required', 'numeric', 'min:0'];
                $rules["variants.{$key}.compare_at_price"] = ['nullable', 'numeric', 'min:0'];
                $rules["variants.{$key}.stock"] = ['nullable', 'integer', 'min:0'];
                $rules["variants.{$key}.sku"] = [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('product_variants', 'sku')->ignore($variant['id'] ?? null),
                ];
            }
            
            
            $rules['variant_attribute_ids'] = ['required', 'array', 'min:1', 'max:3'];
            $rules['variant_attribute_ids.*'] = ['integer', 'exists:attributes,id'];
            
            
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'code.required' => 'Mã sản phẩm là bắt buộc.',
            'code.string' => 'Mã sản phẩm phải là chuỗi ký tự.',
            'code.max' => 'Mã sản phẩm không được vượt quá 255 ký tự.',
            'code.unique' => 'Mã sản phẩm đã tồn tại trong hệ thống.',
            'brand_id.integer' => 'Thương hiệu không hợp lệ.',
            'brand_id.exists' => 'Thương hiệu đã chọn không tồn tại.',
            'category_id.required' => 'Danh mục sản phẩm là bắt buộc.',
            'category_id.integer' => 'Danh mục không hợp lệ.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
            'cate_type.required' => 'Danh mục sản phẩm là bắt buộc.',
            'cate_type.in' => 'Loại danh mục không hợp lệ. Chỉ chấp nhận "Sản phẩm" hoặc "Dịch vụ".',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'content.string' => 'Nội dung phải là chuỗi.',
            'image.required' => 'Ảnh đại diện là bắt buộc khi tạo mới.',
            'image.image' => 'Ảnh đại diện không hợp lệ.',
            'image.mimes' => 'Ảnh đại diện phải là một trong các định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'gallery.array' => 'Thư viện ảnh không hợp lệ.',
            'gallery.*.image' => 'Tất cả ảnh trong thư viện phải là hình ảnh hợp lệ.',
            'gallery.*.mimes' => 'Mỗi ảnh trong thư viện phải có định dạng jpeg, png, jpg, gif, webp.',
            'gallery.*.max' => 'Ảnh trong thư viện không được vượt quá 2MB.',
            'price_discount.required_if' => 'Giá bán là bắt buộc khi không có biến thể.',
            'price_discount.numeric' => 'Giá bán phải là một số.',
            'price_discount.min' => 'Giá bán không được nhỏ hơn 0.',
            'price.required_if' => 'Giá so sánh là bắt buộc khi không có biến thể.',
            'price.numeric' => 'Giá so sánh phải là một số.',
            'price.min' => 'Giá so sánh không được nhỏ hơn 0.',
            'price.gte' => 'Giá so sánh phải lớn hơn hoặc bằng giá bán.',
            'has_variants.boolean' => 'Trường "Có biến thể" không hợp lệ.',
            'variants.required' => 'Vui lòng tạo ít nhất một biến thể.',
            'variants.array' => 'Danh sách biến thể không hợp lệ.',
            'variants.min' => 'Vui lòng tạo ít nhất một biến thể.',
            'variants.*.price.required' => 'Giá của biến thể là bắt buộc.',
            'variants.*.price.numeric' => 'Giá của biến thể phải là một số.',
            'variants.*.price.min' => 'Giá của biến thể phải lớn hơn hoặc bằng 0.',
            'variants.*.stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',
            'variants.*.sku.string' => 'SKU của biến thể phải là chuỗi ký tự.',
            'variants.*.sku.max' => 'SKU của biến thể không được vượt quá 255 ký tự.',
            'variants.*.sku.unique' => 'SKU của biến thể đã tồn tại.',
            'variants.*.id.integer' => 'ID biến thể không hợp lệ.',
            'variants.*.id.exists' => 'ID biến thể không tồn tại.',
            'variants.*._delete.boolean' => 'Trạng thái xoá của biến thể không hợp lệ.',
            'variant_attribute_ids.required' => 'Bạn phải chọn ít nhất một thuộc tính biến thể.',
            'variant_attribute_ids.min' => 'Bạn phải chọn ít nhất một thuộc tính biến thể.',
            'variant_attribute_ids.max' => 'Bạn chỉ được chọn tối đa 3 thuộc tính biến thể.',
            'variant_attribute_ids.*.integer' => 'ID thuộc tính không hợp lệ.',
            'variant_attribute_ids.*.exists' => 'Thuộc tính đã chọn không tồn tại.',
            'attribute_values.*.required' => 'Giá trị thuộc tính là bắt buộc.',
            'attribute_values.*.array' => 'Giá trị thuộc tính không hợp lệ.',
            'attribute_values.*.min' => 'Mỗi thuộc tính phải có ít nhất một giá trị được chọn.',
            'attribute_values.*.*.integer' => 'ID giá trị thuộc tính không hợp lệ.',
            'attribute_values.*.*.exists' => 'Giá trị thuộc tính đã chọn không tồn tại.',
        ];
    }
}