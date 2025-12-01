<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Slug;
use Illuminate\Support\Facades\Schema;

class SlugObserver
{
    /**
     * Xử lý sau khi bản ghi đã được Lưu (cả Create và Update)
     */
    public function saved($model)
    {
        // 1. Xác định nguồn để tạo slug
        $sourceString = null;

        // Nếu Controller gán thủ công: $model->custom_slug = 'abc';
        // (Anh cần gán biến này trong controller nếu muốn custom)
        if (!empty($model->custom_slug)) {
            $sourceString = $model->custom_slug;
        } 
        // Nếu tên/tiêu đề thay đổi hoặc chưa có slug trong bảng relation
        elseif ($model->isDirty('name') || $model->isDirty('title')) {
            $sourceString = $model->name ?? $model->title;
        }
        // Trường hợp tạo mới mà chưa có slug
        elseif ($model->wasRecentlyCreated) {
            $sourceString = $model->name ?? $model->title;
        }

        // 2. Nếu cần tạo/update slug
        if ($sourceString) {
            $this->processSlug($model, $sourceString);
        }
    }

    public function deleting($model)
    {
        // Xóa slug trong bảng slugs khi model bị xóa
        // Dùng relation slugData() như đã fix ở Model Category
        if (method_exists($model, 'slugData') && $model->slugData()->exists()) {
            $model->slugData()->delete();
        }
    }

    /** ====================== CORE ====================== **/

    protected function processSlug($model, $string)
    {
        // 1. Tạo slug base
        $slugBase = Str::slug($string);
        
        // 2. Đảm bảo unique toàn cục
        $finalSlug = $this->uniqueSlug($slugBase, $model);

        // 3. Lưu vào bảng `slugs` (Bảng polymorphic)
        // [QUAN TRỌNG] Dùng slugData() thay vì slug() để tránh trùng tên cột
        if (method_exists($model, 'slugData')) {
            $model->slugData()->updateOrCreate(
                [], // Điều kiện tìm (rỗng vì quan hệ 1-1 sẽ tự fill ID)
                ['slug' => $finalSlug]
            );
        }

        // 4. [Optional] Nếu bảng chính (vd: categories) cũng có cột slug
        // Ta update luôn cột đó để đồng bộ dữ liệu (fallback)
        if (Schema::hasColumn($model->getTable(), 'slug')) {
            // Kiểm tra để tránh loop vô tận và tránh query thừa
            if ($model->slug !== $finalSlug) {
                $model->slug = $finalSlug;
                $model->saveQuietly(); // Lưu mà không kích hoạt lại Observer
            }
        }
    }

    protected function uniqueSlug(string $base, $model): string
    {
        $slug = $base ?: 'no-name';
        $originalSlug = $slug;
        $i = 1;

        // Loop check trùng: Check cả bảng slugs VÀ bảng chính của model
        while ($this->checkExists($slug, $model)) {
            $slug = $originalSlug . '-' . $i++;
        }

        return $slug;
    }

    protected function checkExists($slug, $model): bool
    {
        $id = $model->id;
        $type = get_class($model);

        // 1. Check trong bảng 'slugs' (trừ chính nó)
        $existsInSlugs = Slug::where('slug', $slug)
            ->where(function ($q) use ($id, $type) {
                $q->where('sluggable_type', '!=', $type)
                  ->orWhere('sluggable_id', '!=', $id);
            })->exists();

        if ($existsInSlugs) return true;

        // 2. Check trong bảng chính của model (nếu có cột slug)
        // Để tránh trùng với các record cũ chưa migrate sang bảng slugs
        if (Schema::hasColumn($model->getTable(), 'slug')) {
            $existsInOwnTable = \DB::table($model->getTable())
                ->where('slug', $slug)
                ->where('id', '!=', $id)
                ->exists();
            
            if ($existsInOwnTable) return true;
        }

        return false;
    }
}