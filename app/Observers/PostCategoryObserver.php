<?php

namespace App\Observers;

use App\Models\PostCategory;
use Illuminate\Support\Facades\Cache;

class PostCategoryObserver
{
    /**
     * Hàm hỗ trợ xóa cache cây danh mục (Logic mới thêm vào)
     * Tách ra function riêng cho gọn code
     */
    private function clearTreeCache(PostCategory $postCategory)
    {
        // 1. Xóa cache của chính danh mục này (cái mà hàm getTreeIds tạo ra)
        Cache::forget("post_category_tree_{$postCategory->id}");

        // 2. Nếu nó có cha, xóa luôn cache của cha để nó cập nhật lại danh sách con
        if ($postCategory->parent_id) {
            Cache::forget("post_category_tree_{$postCategory->parent_id}");
        }
    }

    /**
     * Sự kiện saved chạy sau khi created hoặc updated thành công
     */
    public function saved(PostCategory $postCategory): void
    {
        // [Logic Cũ] Giữ nguyên không động vào
        Cache::forget('shared_post_categories_menu');

        // [Logic Mới] Xóa cache cây ID đệ quy
        $this->clearTreeCache($postCategory);
    }

    public function deleted(PostCategory $postCategory): void
    {
        // [Logic Cũ] Giữ nguyên không động vào
        Cache::forget('shared_post_categories_menu');

        // [Logic Mới] Xóa cache cây ID đệ quy
        $this->clearTreeCache($postCategory);
    }
}