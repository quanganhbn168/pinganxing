<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DuplicatorService
{
    /**
     * $cfg lấy từ config/duplicate.php (1 entry của alias)
     */
    public function duplicate(Model $record, array $cfg): Model
    {
        return DB::transaction(function () use ($record, $cfg) {
            $columns   = $cfg['columns']     ?? [];
            $files     = $cfg['files']       ?? [];
            $except    = $cfg['except']      ?? [];
            $relations = $cfg['relations']   ?? [];
            $suffix    = $cfg['name_suffix'] ?? ' (Bản sao)';

            $copy = $record->replicate($except);

            // 1) Copy file fields
            foreach ($files as $field) {
                $path = $record->getAttribute($field);
                if (!$path) continue;

                $pathNoDomain = ltrim(parse_url($path, PHP_URL_PATH) ?: '', '/');   // 'storage/xxx' hoặc 'uploads/xxx'
                $clean        = Str::startsWith($pathNoDomain, 'storage/') ? Str::after($pathNoDomain, 'storage/') : $pathNoDomain;

                if (Storage::disk('public')->exists($clean)) {
                    $info = pathinfo($clean);
                    $dir  = ($info['dirname'] ?? '') === '.' ? '' : $info['dirname'].'/';
                    $name = ($info['filename'] ?? 'file') . '-' . Str::random(5);
                    $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
                    $new  = $dir . $name . $ext;

                    Storage::disk('public')->copy($clean, $new);
                    $useStorage = Str::startsWith($pathNoDomain, 'storage/');
                    $copy->setAttribute($field, $useStorage ? 'storage/'.$new : $new);
                }
            }

            // 2) Name/Title
            if (($col = $columns['name'] ?? null) && Schema::hasColumn($record->getTable(), $col) && $record->getAttribute($col) !== null) {
                $base = $record->getAttribute($col) . $suffix;
                $copy->setAttribute($col, $this->uniqueText($record, $col, $base));
            } elseif (($col = $columns['title'] ?? null) && Schema::hasColumn($record->getTable(), $col) && $record->getAttribute($col) !== null) {
                $base = $record->getAttribute($col) . $suffix;
                $copy->setAttribute($col, $this->uniqueText($record, $col, $base));
            }

            // 3) Slug
            if (($col = $columns['slug'] ?? null) && Schema::hasColumn($record->getTable(), $col)) {
                $base = $record->getAttribute($col)
                    ?: (($columns['name'] ?? null) ? ($copy->getAttribute($columns['name']) ?? Str::random(8)) : Str::random(8));
                $copy->setAttribute($col, $this->uniqueSlug($record, $col, Str::slug($base)));
            }

            // 4) Status -> 0
            if (($col = $columns['status'] ?? null) && Schema::hasColumn($record->getTable(), $col)) {
                $copy->setAttribute($col, 0);
            }

            $copy->save();

            // 5) Quan hệ
            foreach ($relations as $name => $relCfg) {
                $type = strtolower($relCfg['type'] ?? '');
                if ($type === 'belongstomany' && $record->{$name}() instanceof BelongsToMany) {
                    $copy->{$name}()->sync($record->{$name}->pluck('id'));
                    continue;
                }
                if ($type === 'hasmany' && $record->{$name}() instanceof HasMany) {
                    $fk    = $relCfg['fk']    ?? null;
                    $rfile = $relCfg['files'] ?? [];
                    foreach ($record->{$name} as $child) {
                        $childCopy = $child->replicate([]);
                        if ($fk) $childCopy->setAttribute($fk, $copy->getKey());

                        foreach ($rfile as $cf) {
                            $p = $child->getAttribute($cf);
                            if (!$p) continue;

                            $pPath  = ltrim(parse_url($p, PHP_URL_PATH) ?: '', '/');
                            $pClean = Str::startsWith($pPath,'storage/') ? Str::after($pPath,'storage/') : $pPath;

                            if (Storage::disk('public')->exists($pClean)) {
                                $info = pathinfo($pClean);
                                $dir  = ($info['dirname'] ?? '') === '.' ? '' : $info['dirname'].'/';
                                $nm   = ($info['filename'] ?? 'file') . '-' . Str::random(5);
                                $ext  = isset($info['extension']) ? '.' . $info['extension'] : '';
                                $np   = $dir.$nm.$ext;

                                Storage::disk('public')->copy($pClean, $np);
                                $useStorage = Str::startsWith($pPath,'storage/');
                                $childCopy->setAttribute($cf, $useStorage ? 'storage/'.$np : $np);
                            }
                        }

                        $childCopy->save();
                    }
                }
            }

            return $copy->fresh();
        });
    }

    // ===== helpers =====
    private function uniqueText(Model $ref, string $column, string $base): string
    {
        $try = $base; $i = 2;
        while ($ref->newQuery()->where($column, $try)->exists()) {
            $try = $base . ' ' . $i;
            $i++;
        }
        return $try;
    }

    private function uniqueSlug(Model $ref, string $column, string $base): string
    {
        $slug = $base; $i = 2;
        while ($ref->newQuery()->where($column, $slug)->exists()) {
            $slug = Str::slug($base) . '-' . $i;
            $i++;
        }
        return $slug;
    }
}
