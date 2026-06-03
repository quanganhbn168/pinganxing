<?php

namespace App\Support;

use App\Models\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SlugGenerator
{
    public function generate(string $source, ?Model $model = null, ?int $ignoreSlugId = null): string
    {
        $base = Str::slug($source);
        $slug = $base !== '' ? $base : 'no-name';
        $original = $slug;
        $counter = 1;

        while ($this->exists($slug, $model, $ignoreSlugId)) {
            $slug = $original.'-'.$counter++;
        }

        return $slug;
    }

    public function syncModel(Model $model, string $source, ?int $ignoreSlugId = null): string
    {
        if (! $ignoreSlugId && $model->exists && method_exists($model, 'slugData')) {
            $ignoreSlugId = $model->slugData()->value('id');
        }

        $slug = $this->generate($source, $model, $ignoreSlugId);

        if (method_exists($model, 'slugData')) {
            $model->slugData()->updateOrCreate([], ['slug' => $slug]);
            $model->unsetRelation('slugData');
        }

        if (Schema::hasColumn($model->getTable(), 'slug') && $model->slug !== $slug) {
            $model->slug = $slug;
            $model->saveQuietly();
        }

        return $slug;
    }

    protected function exists(string $slug, ?Model $model = null, ?int $ignoreSlugId = null): bool
    {
        $query = Slug::query()->where('slug', $slug);

        if ($ignoreSlugId) {
            $query->where('id', '!=', $ignoreSlugId);
        }

        if ($model) {
            $query->whereIn('sluggable_type', $this->sluggableTypesForSamePrefix($model::class));
        }

        if ($query->exists()) {
            return true;
        }

        if ($model && Schema::hasColumn($model->getTable(), 'slug')) {
            $tableQuery = DB::table($model->getTable())->where('slug', $slug);

            if ($model->getKey()) {
                $tableQuery->where($model->getKeyName(), '!=', $model->getKey());
            }

            if ($tableQuery->exists()) {
                return true;
            }
        }

        return false;
    }

    protected function sluggableTypesForSamePrefix(string $modelClass): array
    {
        if (! method_exists($modelClass, 'slugPrefixMap')) {
            return [$modelClass];
        }

        $prefixMap = $modelClass::slugPrefixMap();
        $prefix = $prefixMap[$modelClass] ?? null;

        if ($prefix === null) {
            return [$modelClass];
        }

        return collect($prefixMap)
            ->filter(fn (?string $mappedPrefix) => $mappedPrefix === $prefix)
            ->keys()
            ->values()
            ->all();
    }
}
