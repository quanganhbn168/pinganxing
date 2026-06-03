<?php

namespace App\Services\ProductImport;

use App\Models\Category;
use App\Models\ProductImportCategoryMap;
use App\Models\ProductImportProfile;
use App\Models\ProductImportRow;
use App\Support\SlugGenerator;
use Illuminate\Support\Str;

class CategoryResolver
{
    public function __construct(protected SlugGenerator $slugGenerator)
    {
    }

    public function resolve(ProductImportRow $row, ?string $categoryPath = null): array
    {
        $profile = $row->batch?->profile;
        $sheetName = (string) $row->sheet?->name;

        if (filled($categoryPath)) {
            $category = $this->resolvePath($categoryPath, $this->profileAllowsCategoryCreation($profile));

            if ($category) {
                return [
                    'category_id' => $category->id,
                    'category_path' => $this->categoryPath($category),
                    'confidence' => 'category_path',
                ];
            }
        }

        $mapped = $this->lookupMap($sheetName, 'sheet', $profile);

        if ($mapped) {
            $category = $mapped->category;

            if (! $category && filled($mapped->category_path)) {
                $category = $this->resolvePath($mapped->category_path, $mapped->auto_create);
            }

            if ($category) {
                return [
                    'category_id' => $category->id,
                    'category_path' => $this->categoryPath($category),
                    'confidence' => 'mapped_'.$mapped->source_type,
                ];
            }
        }

        $category = $this->findByName($sheetName);

        if ($category) {
            return [
                'category_id' => $category->id,
                'category_path' => $this->categoryPath($category),
                'confidence' => 'existing_sheet_name',
            ];
        }

        return [
            'category_id' => null,
            'category_path' => filled($categoryPath) ? $categoryPath : $sheetName,
            'confidence' => null,
        ];
    }

    public function resolvePath(string $path, bool $autoCreate = false): ?Category
    {
        $parts = collect(preg_split('/\s*(?:>|\/|\||\\\\)\s*/u', $path) ?: [])
            ->map(fn (string $part) => trim($part))
            ->filter()
            ->values();

        if ($parts->isEmpty()) {
            return null;
        }

        $parentId = 0;
        $category = null;

        foreach ($parts as $part) {
            $category = $this->findByName($part, $parentId);

            if (! $category && $autoCreate) {
                $category = Category::create([
                    'parent_id' => $parentId,
                    'name' => $part,
                    'status' => true,
                    'is_home' => false,
                    'is_menu' => true,
                    'is_footer' => true,
                ]);

                $this->slugGenerator->syncModel($category, $part);
            }

            if (! $category) {
                return null;
            }

            $parentId = (int) $category->id;
        }

        return $category;
    }

    public function categoryPath(Category $category): string
    {
        $names = [];
        $current = $category;

        while ($current) {
            array_unshift($names, $current->name);

            if (! $current->parent_id) {
                break;
            }

            $current = Category::query()->find($current->parent_id);
        }

        return implode(' / ', $names);
    }

    protected function lookupMap(string $value, string $type, ?ProductImportProfile $profile): ?ProductImportCategoryMap
    {
        if (! filled($value)) {
            return null;
        }

        return ProductImportCategoryMap::query()
            ->with('category')
            ->where('is_active', true)
            ->where('source_type', $type)
            ->where('normalized_value', ProductImportCategoryMap::normalizeSource($value))
            ->where(function ($query) use ($profile) {
                $query->whereNull('product_import_profile_id');

                if ($profile) {
                    $query->orWhere('product_import_profile_id', $profile->id);
                }
            })
            ->orderByRaw('product_import_profile_id is null asc')
            ->orderByDesc('priority')
            ->first();
    }

    protected function findByName(string $name, ?int $parentId = null): ?Category
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        return Category::query()
            ->when($parentId !== null, fn ($query) => $query->where('parent_id', $parentId ?: 0))
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();
    }

    protected function profileAllowsCategoryCreation(?ProductImportProfile $profile): bool
    {
        return (bool) data_get($profile?->options ?? [], 'auto_create_categories', false);
    }
}
