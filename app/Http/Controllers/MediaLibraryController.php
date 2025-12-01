<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\MediaFile; // Nhớ use Model này

class MediaLibraryController extends Controller
{
    protected $disk;
    protected $root;

    public function __construct()
    {
        $this->disk = config('media_local.disk', 'public');
        $this->root = trim(config('media_local.originals_root', 'userfiles/images'), '/');
    }

    /**
     * Lấy danh sách ảnh (Từ Database)
     */
    public function index(Request $request)
    {
        // Nếu DB trống trơn, tự động chạy sync lần đầu
        if (MediaFile::count() === 0) {
            MediaFile::syncFromDisk($this->disk, $this->root);
        }

        // Query từ Database
        $query = MediaFile::query()
            ->where('disk', $this->disk)
            ->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($s = $request->input('s')) {
            $query->where('filename', 'like', "%{$s}%");
        }

        // Phân trang
        $perPage = (int) $request->input('per_page', 50);
        $files = $query->paginate($perPage);

        // Format dữ liệu trả về
        $data = $files->getCollection()->map(function ($file) {
            return [
                'id'   => $file->id,
                'path' => $file->path,
                // URL hiển thị
                'url'  => Storage::disk($file->disk)->url($file->path),
                'name' => $file->filename,
                // Size đẹp
                'size' => $this->humanFileSize($file->size),
                'time' => $file->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'data'         => $data,
            'total'        => $files->total(),
            'per_page'     => $files->perPage(),
            'current_page' => $files->currentPage(),
            'last_page'    => $files->lastPage(),
        ]);
    }

    /**
     * Upload file mới
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,webp,gif,svg|max:10240', // 10MB
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {
            $originalName = $file->getClientOriginalName();
            $name = pathinfo($originalName, PATHINFO_FILENAME);
            $ext  = strtolower($file->getClientOriginalExtension());

            // Tạo tên an toàn
            $safeName = Str::slug($name) . '.' . $ext;
            $path = "{$this->root}/{$safeName}";

            // Xử lý trùng tên
            $i = 1;
            while (Storage::disk($this->disk)->exists($path)) {
                $path = "{$this->root}/" . Str::slug($name) . "-{$i}.{$ext}";
                $i++;
            }

            // 1. Lưu vật lý
            $storedPath = $file->storeAs($this->root, basename($path), $this->disk);

            if ($storedPath) {
                // 2. Lưu Database
                $media = MediaFile::create([
                    'disk'       => $this->disk,
                    'path'       => $storedPath,
                    'filename'   => basename($storedPath),
                    'extension'  => $ext,
                    'mime_type'  => $file->getMimeType(),
                    'size'       => $file->getSize(),
                ]);

                $uploaded[] = [
                    'path' => $media->path,
                    'url'  => Storage::disk($this->disk)->url($media->path),
                    'name' => $media->filename,
                ];
            }
        }

        return response()->json(['success' => true, 'data' => $uploaded]);
    }

    /**
     * Xóa file
     */
    public function destroy(Request $request)
    {
        $path = $request->input('path');
        if (!$path) return response()->json(['success' => false], 400);

        $file = MediaFile::where('disk', $this->disk)->where('path', $path)->first();

        if ($file) {
            if (Storage::disk($this->disk)->exists($file->path)) {
                Storage::disk($this->disk)->delete($file->path);
            }
            $file->delete();
            return response()->json(['success' => true]);
        }

        // Fallback: Xóa file rác ko có trong DB
        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    /**
     * [MỚI] API ĐỒNG BỘ (SYNC)
     */
    public function sync()
    {
        try {
            // Gọi hàm sync tĩnh trong Model MediaFile
            MediaFile::syncFromDisk($this->disk, $this->root);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Helper format size
    private function humanFileSize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}