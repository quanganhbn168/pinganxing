<?php

namespace App\Handlers;

use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;
class ImageGalleryHandler
{
    use UploadImageTrait;
    public function sync(object $model, Request $request, string $field = 'gallery', string $folder = 'uploads/gallery', int $resize = 800): void
    {
        // Lấy ra ID các ảnh cũ cần giữ lại và ép kiểu sang integer
        $idsToKeep = array_map('intval', $request->input("{$field}_old", []));

        // Lấy ra ID của tất cả ảnh hiện tại trong DB
        $currentIds = $model->images()->pluck('id')->toArray();
        
        $newImages = $request->file($field, []);

        // Tối ưu: Nếu không có ảnh mới và danh sách ảnh cũ không đổi thì không làm gì cả
        if (empty($newImages) && count($idsToKeep) === count($currentIds) && empty(array_diff($currentIds, $idsToKeep))) {
            return;
        }

        // Tìm ra các ID cần xóa
        $idsToDelete = array_diff($currentIds, $idsToKeep);

        if (!empty($idsToDelete)) {
            $imagesToDelete = $model->images()->whereIn('id', $idsToDelete)->get();
            foreach ($imagesToDelete as $imageRecord) {
                // Xóa file vật lý bằng phương thức từ Trait
                $this->deleteImage($imageRecord->image);
            }
            // Xóa các bản ghi khỏi CSDL
            $model->images()->whereIn('id', $idsToDelete)->delete();
        }

        // Thêm ảnh mới
        if (!empty($newImages)) {
            foreach ($newImages as $imageFile) {
                // Sử dụng Trait uploadImage đã có sẵn
                $path = $this->uploadImage($imageFile, $folder, $resize, $resize, true);
                
                // [SỬA LỖI] Sử dụng relationship "images()" để tạo bản ghi ảnh mới
                $model->images()->create(['image' => $path]);
            }
        }
    }

    public function deleteAll(object $model): void
    {
        // Lấy tất cả các bản ghi ảnh của model
        $galleryImages = $model->images()->get();

        foreach ($galleryImages as $image) {
            // 1. Xóa file vật lý khỏi storage
            if (method_exists($model, 'deleteImage')) {
                $model->deleteImage($image->image);
            }
        }

        // 2. Xóa tất cả các bản ghi trong database bằng một câu lệnh
        $model->images()->delete();
    }
}
