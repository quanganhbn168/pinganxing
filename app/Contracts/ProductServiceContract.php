<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Interface định nghĩa các chức năng cho việc quản lý Sản phẩm.
 */
interface ProductServiceContract
{
    /**
     * Tạo một sản phẩm mới.
     *
     * @param FormRequest $request Dữ liệu đã validate từ form.
     * @return Product Sản phẩm vừa được tạo.
     */
    public function create(FormRequest $request): Product;

    /**
     * Cập nhật một sản phẩm đã có.
     *
     * @param FormRequest $request Dữ liệu đã validate từ form.
     * @param Product $product Sản phẩm cần cập nhật.
     * @return Product Sản phẩm sau khi cập nhật.
     */
    public function update(FormRequest $request, Product $product): Product;

    /**
     * Xóa một sản phẩm.
     *
     * @param Product $product Sản phẩm cần xóa.
     * @return void
     */
    public function delete(Product $product): void;
}