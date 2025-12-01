<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Duplicateable
{
    /**
     * API chính: $model->duplicate()
     */
    public function duplicate(): Model
    {
        return DB::transaction(function () {
            // ----- Lấy cấu hình áp dụng -----
            $cfg       = $this->resolveDuplicateConfig();        // từ config + override model
            $columns   = $cfg['columns'];
            $files     = $cfg['files'];
            $except    = $cfg['except'];
            $relations = $cfg['relations'];
            $suffix    = $cfg['name_suffix'] ?? ' (Bản sao)';

            // ----- Replicate base model -----
            $newModel = $this->replicate($except);

            // 1) Copy file fields
            foreach ($files as $fileField) {
                $path = $this->getAttribute($fileField);
                if (!$path) continue;

                $pathNoDomain = ltrim(parse_url($path, PHP_URL_PATH) ?: '', '/'); // 'storage/xxx' or 'uploads/xxx'
                $clean = Str::startsWith($pathNoDomain, 'storage/')
                    ? Str::after($pathNoDomain, 'storage/')
                    : $pathNoDomain;

                if (Storage::disk('public')->exists($clean)) {
                    $info = pathinfo($clean);
                    $dir  = ($info['dirname'] ?? '') === '.' ? '' : $info['dirname'] . '/';
                    $name = ($info['filename'] ?? 'file') . '-' . Str::random(5);
                    $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
                    $newRel = $dir . $name . $ext;

                    Storage::disk('public')->copy($clean, $newRel);

                    $useStoragePrefix = Str::startsWith($pathNoDomain, 'storage/');
                    $newModel->setAttribute($fileField, $useStoragePrefix ? 'storage/' . $newRel : $newRel);
                }
            }

            // 2) Name/Title: thêm hậu tố + unique nhẹ
            if ($columns['name'] && Schema::hasColumn($this->getTable(), $columns['name']) && $this->getAttribute($columns['name']) !== null) {
                $base = $this->getAttribute($columns['name']) . $suffix;
                $newModel->setAttribute($columns['name'], $this->makeUniqueText($columns['name'], $base));
            } elseif ($columns['title'] && Schema::hasColumn($this->getTable(), $columns['title']) && $this->getAttribute($columns['title']) !== null) {
                $base = $this->getAttribute($columns['title']) . $suffix;
                $newModel->setAttribute($columns['title'], $this->makeUniqueText($columns['title'], $base));
            }

            // 3) Slug: unique -2/-3...
            if ($columns['slug'] && Schema::hasColumn($this->getTable(), $columns['slug'])) {
                $base = $this->getAttribute($columns['slug'])
                    ?: ($columns['name'] ? ($newModel->getAttribute($columns['name']) ?? Str::random(8)) : Str::random(8));
                $newModel->setAttribute($columns['slug'], $this->makeUniqueSlug($columns['slug'], Str::slug($base)));
            }

            // 4) Status: set 0 nếu có cột
            if ($columns['status'] && Schema::hasColumn($this->getTable(), $columns['status'])) {
                $newModel->setAttribute($columns['status'], 0);
            }

            $newModel->save();

            // 5) Clone relations
            foreach ($relations as $relationName => $relCfg) {
                $type = strtolower($relCfg['type'] ?? '');
                if ($type === 'belongstomany' && $this->{$relationName}() instanceof BelongsToMany) {
                    $newModel->{$relationName}()->sync($this->{$relationName}->pluck('id'));
                    continue;
                }
                if ($type === 'hasmany' && $this->{$relationName}() instanceof HasMany) {
                    $fk      = $relCfg['fk']     ?? null;
                    $rFiles  = $relCfg['files']  ?? []; // file fields ở child
                    foreach ($this->{$relationName} as $child) {
                        $exceptChild = method_exists($child, 'getExceptForReplication') ? $child->getExceptForReplication() : [];
                        $childCopy   = $child->replicate($exceptChild);
                        if ($fk) $childCopy->setAttribute($fk, $newModel->getKey());

                        // copy file của child nếu có
                        foreach ($rFiles as $cf) {
                            $p = $child->getAttribute($cf);
                            if (!$p) continue;

                            $pPath = ltrim(parse_url($p, PHP_URL_PATH) ?: '', '/');
                            $pClean = Str::startsWith($pPath, 'storage/') ? Str::after($pPath, 'storage/') : $pPath;

                            if (Storage::disk('public')->exists($pClean)) {
                                $info = pathinfo($pClean);
                                $dir  = ($info['dirname'] ?? '') === '.' ? '' : $info['dirname'] . '/';
                                $name = ($info['filename'] ?? 'file') . '-' . Str::random(5);
                                $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
                                $newP = $dir . $name . $ext;

                                Storage::disk('public')->copy($pClean, $newP);
                                $useStoragePrefix = Str::startsWith($pPath, 'storage/');
                                $childCopy->setAttribute($cf, $useStoragePrefix ? 'storage/' . $newP : $newP);
                            }
                        }

                        $childCopy->save();
                    }
                }
            }

            return $newModel->fresh();
        });
    }

    // ========== Helpers & config resolution ==========

    /**
     * Trộn cấu hình từ config/duplicate.php và override từ model (nếu có).
     */
    protected function resolveDuplicateConfig(): array
    {
        $alias = property_exists($this, 'duplicateableAlias') ? $this->duplicateableAlias : null;
        $map   = config('duplicate.models', []);

        // Ưu tiên alias nếu có
        $cfg = $alias && isset($map[$alias]) ? $map[$alias] : null;

        // Nếu không có alias, tìm theo class
        if (!$cfg) {
            foreach ($map as $entry) {
                if (($entry['class'] ?? null) === static::class) {
                    $cfg = $entry;
                    break;
                }
            }
        }

        $cfg = $cfg ?: [];

        // columns mặc định
        $columns = array_merge([
            'name'   => Schema::hasColumn($this->getTable(), 'name')   ? 'name'   : null,
            'title'  => Schema::hasColumn($this->getTable(), 'title')  ? 'title'  : null,
            'slug'   => Schema::hasColumn($this->getTable(), 'slug')   ? 'slug'   : null,
            'status' => Schema::hasColumn($this->getTable(), 'status') ? 'status' : null,
        ], $cfg['columns'] ?? []);

        // files / relations / except từ model override nếu có
        $files     = property_exists($this, 'duplicateableFiles')      ? $this->duplicateableFiles      : ($cfg['files'] ?? []);
        $relations = property_exists($this, 'duplicateableRelations')  ? $this->duplicateableRelations  : ($cfg['relations'] ?? []);
        $except    = property_exists($this, 'exceptForReplication')    ? $this->exceptForReplication    : ($cfg['except'] ?? []);

        return [
            'columns'     => $columns,
            'files'       => $files,
            'relations'   => $relations,
            'except'      => $except,
            'name_suffix' => $cfg['name_suffix'] ?? ' (Bản sao)',
            'edit_route'  => $cfg['edit_route']  ?? null,
        ];
    }

    protected function makeUniqueText(string $column, string $base): string
    {
        $try = $base; $i = 2;
        while ($this->newQuery()->where($column, $try)->exists()) {
            $try = $base . ' ' . $i;
            $i++;
        }
        return $try;
    }

    protected function makeUniqueSlug(string $column, string $base): string
    {
        $slug = $base; $i = 2;
        while ($this->newQuery()->where($column, $slug)->exists()) {
            $slug = Str::slug($base) . '-' . $i;
            $i++;
        }
        return $slug;
    }

    // Back-compat: nếu model có mấy hàm bên dưới thì trait vẫn tôn trọng
    protected function getRelationsToDuplicate(): array
    {
        return property_exists($this, 'duplicateableRelations') ? $this->duplicateableRelations : [];
    }
    protected function getFilesToDuplicate(): array
    {
        return property_exists($this, 'duplicateableFiles') ? $this->duplicateableFiles : [];
    }
    protected function getExceptForReplication(): array
    {
        return property_exists($this, 'exceptForReplication') ? $this->exceptForReplication : [];
    }
}
