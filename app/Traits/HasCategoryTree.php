<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasCategoryTree
 *
 * Dùng chung cho tất cả model Category có cấu trúc cây (parent_id).
 * Cung cấp:
 *  - Quan hệ: parent(), children(), childrenRecursive()
 *  - Helper tĩnh: getParentOptions($excludeId) — lọc loại chính nó + con cháu
 *  - Helper tĩnh: getLeafOptions() — chỉ lấy danh mục lá (không có con)
 */
trait HasCategoryTree
{
    // ─── Relationships ───

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    // ─── Lấy tất cả ID con cháu (đệ quy) ───

    public function getAllDescendantIds(): array
    {
        $ids = [];
        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return $ids;
    }

    /**
     * Lấy ID của chính danh mục và toàn bộ con cháu.
     *
     * @param int $startId
     * @return array<int>
     */
    public static function getTreeIds(int $startId): array
    {
        $record = static::with('childrenRecursive')->find($startId);

        if (! $record) {
            return [$startId];
        }

        return array_merge([$startId], $record->getAllDescendantIds());
    }

    // ─── Dùng cho Select parent_id trong form Category ───

    /**
     * Lấy danh sách options hợp lệ cho trường parent_id.
     * Loại bỏ:
     *  - Chính nó ($excludeId)
     *  - Tất cả con cháu của nó (tránh vòng lặp)
     *
     * @param int|null $excludeId  ID của record đang edit (null khi tạo mới)
     * @return array  [id => name]
     */
    public static function getParentOptions(?int $excludeId = null): array
    {
        $query = static::query()->where('status', true)->orderBy('name');

        // Nếu đang edit, loại bỏ chính nó + tất cả con cháu
        if ($excludeId) {
            $record = static::with('childrenRecursive')->find($excludeId);

            if ($record) {
                $excludeIds = array_merge([$excludeId], $record->getAllDescendantIds());
                $query->whereNotIn('id', $excludeIds);
            }
        }

        return $query->pluck('name', 'id')->toArray();
    }

    // ─── Dùng cho Select category_id trong form Item con ───

    /**
     * Lấy danh sách danh mục LÁ (không có con).
     * Dùng khi gán Post/Project/Service/Field vào danh mục.
     *
     * @return array  [id => name]
     */
    public static function getLeafOptions(): array
    {
        return static::query()
            ->where('status', true)
            ->whereDoesntHave('children')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Lấy danh sách dạng cây phẳng có thụt đầu dòng (cho Select đẹp hơn).
     * Loại bỏ chính nó + con cháu nếu cần.
     *
     * @param int|null $excludeId
     * @param int|null $parentId
     * @param string   $prefix
     * @return array  [id => '── Tên danh mục']
     */
    public static function getTreeOptions(?int $excludeId = null, ?int $parentId = null, string $prefix = ''): array
    {
        $excludeIds = [];

        if ($excludeId) {
            $record = static::with('childrenRecursive')->find($excludeId);

            if ($record) {
                $excludeIds = array_merge([$excludeId], $record->getAllDescendantIds());
            }
        }

        $query = static::query()
            ->where('status', true)
            ->whereNotIn('id', $excludeIds)
            ->orderBy('name');

        if ($parentId === null) {
            $query->where(function ($query) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', 0);
            });
        } else {
            $query->where('parent_id', $parentId);
        }

        $options = [];

        foreach ($query->get() as $item) {
            $options[$item->id] = $prefix . $item->name;

            $childOptions = static::getTreeOptions($excludeId, $item->id, $prefix . '── ');
            $options = $options + $childOptions;
        }

        return $options;
    }
}
