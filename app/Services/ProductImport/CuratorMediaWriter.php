<?php

namespace App\Services\ProductImport;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CuratorMediaWriter
{
    public function ensure(string $disk, string $path, array $attributes = []): Media
    {
        $storage = Storage::disk($disk);
        $fullPath = $storage->path($path);
        $filename = basename($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $directory = trim(str_replace('\\', '/', dirname($path)), '.');
        $size = is_file($fullPath) ? filesize($fullPath) : null;
        $dimensions = is_file($fullPath) ? @getimagesize($fullPath) : null;
        $mime = $dimensions['mime'] ?? (is_file($fullPath) ? @mime_content_type($fullPath) : null);

        $payload = array_merge([
            'disk' => $disk,
            'directory' => $directory !== '' ? $directory : null,
            'visibility' => 'public',
            'name' => Str::limit($name, 250, ''),
            'path' => $path,
            'width' => is_array($dimensions) ? (int) $dimensions[0] : null,
            'height' => is_array($dimensions) ? (int) $dimensions[1] : null,
            'size' => $size ?: 0,
            'type' => $mime ?: 'image/'.$extension,
            'ext' => $extension,
        ], $attributes);

        $media = Media::query()
            ->where('disk', $disk)
            ->where('path', $path)
            ->first();

        if ($media) {
            $media->fill($payload)->save();

            return $media;
        }

        return Media::create($payload);
    }
}
