<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface MediaServiceContract
{
    /**
     * Xử lý ảnh (từ file upload hoặc path), tạo các phiên bản và trả về dữ liệu để lưu vào bảng images.
     * Ảnh gốc sẽ không bị thay đổi nếu đầu vào là path.
     *
     * @param UploadedFile|string $source Nguồn ảnh (UploadedFile hoặc path tương đối trong disk 'public').
     * @param string $destinationDir Thư mục gốc để lưu ảnh đã xử lý (vd: 'products', 'posts'). Sẽ tự tạo thư mục con duy nhất bên trong.
     * @param array $options Tùy chọn xử lý:
     * 'variants' => [ 'thumb' => ['width' => 150, 'height' => 150], 'medium' => [...] ], // Các phiên bản cần tạo
     * 'main' => ['width' => 1200, 'height' => null], // Kích thước cho ảnh chính (main_path)
     * 'format' => 'webp', // Định dạng cuối cùng (webp, jpg, png...)
     * 'quality' => 85, // Chất lượng ảnh
     * 'disk' => 'public' // Disk lưu trữ
     * @return array|null Mảng dữ liệu chứa thông tin ảnh đã xử lý để lưu vào model Image, hoặc null nếu lỗi.
     * Ví dụ trả về: ['dir', 'main_path', 'variants', 'disk', 'original_path', 'filename', 'ext', 'mime', 'size', 'width', 'height']
     */
    public function processAndPrepareData(UploadedFile|string $source, string $destinationDir, array $options = []): ?array;

    /**
     * Xóa ảnh chính và tất cả các phiên bản của nó dựa trên dữ liệu từ model Image.
     *
     * @param \App\Models\Image|array|null $imageData Model Image hoặc mảng chứa thông tin ảnh (cần có 'disk', 'main_path', 'variants').
     * @return bool True nếu xóa thành công ít nhất 1 file, False nếu không có gì để xóa hoặc lỗi.
     */
    public function deleteProcessedImages(\App\Models\Image|array|null $imageData): bool;

    /**
     * Xử lý trọn gói việc cập nhật media cho một model.
     *
     * @param object $model Model (Post, Product, User...)
     * @param string|null $originalPath Path ảnh gốc mới
     * @param string $directory Thư mục lưu (vd: 'posts', 'products')
     * @param array $config Cấu hình xử lý ảnh (chính là $options cho processAndPrepareData)
     * @param callable $newImageSetter Callback để gán ảnh mới vào model (ví dụ: fn($data) => $model->setMainImage($data))
     * @param callable|null $oldImageGetter Callback để lấy ảnh cũ (ví dụ: fn() => $model->mainImage())
     * @param string $logContext Tên bối cảnh (ví dụ: 'ảnh đại diện')
     * @return bool Trả về true nếu xử lý thành công, false nếu thất bại hoặc không có gì làm.
     */
    public function updateMedia(
        object $model,
        ?string $originalPath,
        string $directory,
        array $config,
        callable $newImageSetter,
        ?callable $oldImageGetter = null,
        string $logContext = 'media'
    ): bool;
}