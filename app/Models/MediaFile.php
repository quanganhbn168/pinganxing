<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaFile extends Model
{
    use HasFactory;

    // Khai báo tên bảng (tùy chọn nếu tên bảng là số nhiều của tên Model, nhưng khai báo cho chắc)
    protected $table = 'media_files';

    // Cho phép gán dữ liệu hàng loạt cho các cột này (khớp với ảnh bạn gửi)
    protected $fillable = [
        'disk',
        'path',
        'filename',
        'extension',
        'mime_type',
        'size',
    ];

    /**
     * HÀM ĐỒNG BỘ DỮ LIỆU (QUAN TRỌNG)
     * Quét ổ cứng và cập nhật vào bảng media_files
     */
    public static function syncFromDisk($diskName = 'public', $folder = 'userfiles/images')
    {
        // Tăng giới hạn thời gian và bộ nhớ vì quét file có thể lâu
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        // 1. Lấy tất cả file thực tế trên ổ cứng
        $allFiles = Storage::disk($diskName)->allFiles($folder);
        
        // Các đuôi file cho phép
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp'];
        $realFiles = [];

        // Lọc lấy file ảnh và tạo mảng key=path để so sánh
        foreach ($allFiles as $path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExtensions)) {
                $realFiles[$path] = $path;
            }
        }

        // 2. Lấy danh sách file đang có trong Database
        $dbFiles = self::where('disk', $diskName)
                        ->where('path', 'like', "$folder/%")
                        ->pluck('path', 'path')
                        ->toArray();

        // 3. TÌM FILE CẦN XÓA (Có trong DB nhưng mất trên ổ cứng)
        $toDelete = array_diff_key($dbFiles, $realFiles);
        if (!empty($toDelete)) {
            self::where('disk', $diskName)->whereIn('path', $toDelete)->delete();
        }

        // 4. TÌM FILE CẦN THÊM (Có trên ổ cứng nhưng chưa có trong DB)
        $toCreate = array_diff_key($realFiles, $dbFiles);
        
        if (!empty($toCreate)) {
            $insertData = [];
            $now = now();
            $storage = Storage::disk($diskName);

            foreach ($toCreate as $path) {
                // Lấy thông tin file
                // Dùng try-catch để tránh lỗi nếu file bị lỗi permission hoặc 0 byte
                try {
                    $size = $storage->size($path);
                    $mime = $storage->mimeType($path);
                    $timestamp = $storage->lastModified($path);
                } catch (\Exception $e) {
                    $size = 0;
                    $mime = null;
                    $timestamp = time();
                }

                $insertData[] = [
                    'disk'       => $diskName,
                    'path'       => $path,
                    'filename'   => basename($path),
                    'extension'  => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                    'mime_type'  => $mime,
                    'size'       => $size,
                    'created_at' => date('Y-m-d H:i:s', $timestamp), // Lấy ngày tạo file gốc
                    'updated_at' => $now,
                ];

                // Chèn từng mẻ 100 dòng để tối ưu
                if (count($insertData) >= 100) {
                    self::insert($insertData);
                    $insertData = [];
                }
            }

            // Chèn nốt số còn lại
            if (!empty($insertData)) {
                self::insert($insertData);
            }
        }
        
        return true;
    }
}