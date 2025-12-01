<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Thống kê
        $totalFiles = MediaFile::count();
        $totalSize = $this->humanFilesize(MediaFile::sum('size'));
        $stats = [
            'images' => MediaFile::whereIn('extension', ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])->count(),
            'others' => MediaFile::whereNotIn('extension', ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])->count(),
        ];

        // 2. Lấy danh sách file (Phân trang)
        $query = MediaFile::query()->orderBy('created_at', 'desc');

        if ($request->keyword) {
            $query->where('filename', 'like', '%' . $request->keyword . '%');
        }

        $files = $query->paginate(60);

        // 3. Xử lý URL hiển thị cho View (Quan trọng nhất)
        $files->getCollection()->transform(function ($file) {
            // Tạo đường dẫn HTTP tuyệt đối
            $file->full_url = asset('storage/' . ltrim($file->path, '/'));
            $file->formatted_size = $this->humanFilesize($file->size);
            return $file;
        });

        return view('admin.media.index', compact('files', 'totalFiles', 'totalSize', 'stats'));
    }

    // Hàm format dung lượng
    private function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}