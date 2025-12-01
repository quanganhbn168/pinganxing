<?php

namespace App\Contracts;

use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Interface định nghĩa các chức năng cho việc quản lý Danh mục sản phẩm.
 */
interface ProductCategoryServiceContract
{
    /**
     * Tạo một danh mục sản phẩm mới.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @return \App\Models\ProductCategory
     */
    public function create(FormRequest $request): ProductCategory;

    /**
     * Cập nhật một danh mục sản phẩm đã có.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param \App\Models\ProductCategory $category
     * @return \App\Models\ProductCategory
     */
    public function update(FormRequest $request, ProductCategory $category): ProductCategory;

    /**
     * Xóa một danh mục sản phẩm.
     *
     * @param \App\Models\ProductCategory $category
     * @return void
     */
    public function delete(ProductCategory $category): void;
}