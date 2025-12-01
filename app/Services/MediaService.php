<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Image as ImageModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\NotReadableException;

class MediaService implements MediaServiceContract
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        // Khởi tạo ImageManager với GD driver cho Intervention Image v3
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Xử lý ảnh (từ file upload hoặc path), tạo các phiên bản và trả về dữ liệu để lưu vào bảng images.
     */
    public function processAndPrepareData(UploadedFile|string $source, string $destinationDir, array $options = []): ?array
    {
        try {
            $disk = $options['disk'] ?? 'public';
            $originalPath = null;
            $originalFilenameBase = '';
            $originalExtension = '';

            // --- 1. Đọc ảnh gốc bằng ImageManager v3 ---
            $originalImage = null;
            if ($source instanceof UploadedFile) {
                if (!$source->isValid()) throw new \Exception('Uploaded file is not valid.');
                $originalImage = $this->imageManager->read($source->getRealPath());
                $originalFilenameBase = pathinfo($source->getClientOriginalName(), PATHINFO_FILENAME);
                $originalExtension = strtolower($source->getClientOriginalExtension());
            } elseif (is_string($source) && Storage::disk($disk)->exists($source)) {
                $originalPath = $source;
                $originalImage = $this->imageManager->read(Storage::disk($disk)->path($source));
                $originalFilenameBase = pathinfo($source, PATHINFO_FILENAME);
                $originalExtension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
            } else {
                throw new \Exception('Nguồn ảnh không hợp lệ.');
            }
            // -----------------------------------------

            // --- 2. Chuẩn bị thư mục và tên file mới ---
            $format = strtolower($options['format'] ?? $originalExtension);
            if (!in_array($format, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) $format = 'jpg'; 

            // Tạo thư mục con duy nhất
            $subDir = date('Y/m/d') . '/' . Str::random(10);
            $fullDir = trim($destinationDir, '/') . '/' . $subDir;
            if (!Storage::disk($disk)->exists($fullDir)) {
                Storage::disk($disk)->makeDirectory($fullDir);
            }
            $baseFilename = Str::slug($originalFilenameBase);
            if (empty($baseFilename)) {
                $baseFilename = Str::random(8);
            }

            // --- 3. Xử lý ảnh chính (main_path) ---
            $mainOptions = $options['main'] ?? ['width' => 1200, 'height' => null];
            $quality = $options['quality'] ?? 90;

            // Tạo bản sao bằng cách đọc lại từ dữ liệu gốc
            $mainImage = $this->createImageCopy($originalImage, $source, $disk);
            if (isset($mainOptions['width']) || isset($mainOptions['height'])) {
                $mainImage->scaleDown(
                    $mainOptions['width'] ?? null, 
                    $mainOptions['height'] ?? null
                );
                if (isset($mainOptions['fit']) && $mainOptions['fit'] === true) {
        // cover: Lấp đầy khung, cắt phần thừa (đúng chuẩn 1920x600)
                    $mainImage->cover(
                        $mainOptions['width'], 
                        $mainOptions['height']
                    );
                } else {
        // scaleDown: Giữ tỷ lệ, không cắt ảnh (như cũ)
                    $mainImage->scaleDown(
                        $mainOptions['width'] ?? null, 
                        $mainOptions['height'] ?? null
                    );
                }
            }
            $mainFilename = $baseFilename . '.' . $format;
            $mainPath = $fullDir . '/' . $mainFilename;
            
            // Lưu ảnh với cú pháp v3
            $mainImage->save(Storage::disk($disk)->path($mainPath), $quality);

            // Lấy thông tin metadata ảnh chính
            $mainImageData = [
                'filename' => $mainFilename,
                'ext' => $format,
                'mime' => Storage::disk($disk)->mimeType($mainPath),
                'size' => Storage::disk($disk)->size($mainPath),
                'width' => $mainImage->width(),
                'height' => $mainImage->height(),
            ];

            // --- 4. Xử lý các phiên bản (variants) ---
            $variantsData = [];
            $definedVariants = $options['variants'] ?? ['thumbnail' => ['width' => 150, 'height' => 150]];

            foreach ($definedVariants as $variantName => $variantOptions) {
                try {
                    // Tạo bản sao mới cho mỗi variant
                    $variantImage = $this->createImageCopy($originalImage, $source, $disk);
                    $vWidth = $variantOptions['width'] ?? null;
                    $vHeight = $variantOptions['height'] ?? null;
                    $vQuality = $variantOptions['quality'] ?? $quality;
                    $vFormat = strtolower($variantOptions['format'] ?? $format);

                    if ($vWidth || $vHeight) {
                        // Crop hoặc Fit thay vì chỉ resize
                        if (isset($variantOptions['fit']) && $variantOptions['fit']) {
                            $variantImage->cover($vWidth, $vHeight);
                        } else {
                            $variantImage->scaleDown($vWidth, $vHeight);
                        }
                    }

                    $variantFilename = $baseFilename . '-' . $variantName . '.' . $vFormat;
                    $variantPath = $fullDir . '/' . $variantFilename;
                    
                    // Lưu variant với cú pháp v3
                    $variantImage->save(Storage::disk($disk)->path($variantPath), $vQuality);
                    $variantsData[$variantName] = $variantPath;

                } catch (\Exception $e) {
                    Log::error("MediaService: Lỗi khi tạo variant '{$variantName}' cho ảnh gốc path '{$originalPath}'. Lỗi: " . $e->getMessage());
                    // Bỏ qua variant lỗi và tiếp tục
                }
            }

            // --- 5. Tổng hợp dữ liệu trả về ---
            $returnData = array_merge($mainImageData, [
                'dir' => $fullDir,
                'main_path' => $mainPath,
                'variants' => !empty($variantsData) ? $variantsData : null,
                'original_path' => ($source instanceof UploadedFile) ? $source->getClientOriginalName() : $originalPath,
                'disk' => $disk,
            ]);
            
            $sourceIdentifier = ($source instanceof UploadedFile) ? $source->getClientOriginalName() : $originalPath;
            Log::info("MediaService: Đã xử lý ảnh '{$sourceIdentifier}' thành:", $returnData);
            return $returnData;

        } catch (NotReadableException $e) {
            Log::error("MediaService: Không thể đọc file ảnh nguồn. Kiểm tra file/quyền. Path: " . ($originalPath ?? 'Uploaded File'));
            report($e);
            return null;
        } catch (\Exception $e) {
            Log::error("MediaService: Lỗi không xác định khi xử lý ảnh. Lỗi: " . $e->getMessage());
            report($e);
            return null;
        }
    }

    /**
     * Tạo bản sao của ảnh bằng cách đọc lại từ nguồn
     */
    private function createImageCopy($originalImage, $source, string $disk)
    {
        if ($source instanceof UploadedFile) {
            return $this->imageManager->read($source->getRealPath());
        } elseif (is_string($source)) {
            return $this->imageManager->read(Storage::disk($disk)->path($source));
        }
        
        // Fallback: encode và decode lại ảnh
        return $this->imageManager->read($originalImage->encode()->toString());
    }

    /**
     * Xóa toàn bộ thư mục chứa ảnh (ảnh chính và tất cả phiên bản).
     */
    /**
     * Xóa toàn bộ thư mục chứa ảnh (ảnh chính và tất cả phiên bản).
     */
    /**
     * Xóa toàn bộ thư mục chứa ảnh (ảnh chính và tất cả phiên bản).
     */
    public function deleteProcessedImages(ImageModel|array|null $imageData): bool
    {
        if (!$imageData) return false;

        $disk = 'public'; // Mặc định là public
        $dir = null;

        // Xử lý cả 2 trường hợp: Model và Array
        if ($imageData instanceof ImageModel) {
            // Lấy thông tin từ model Image
            $disk = $imageData->disk ?? 'public';
            $dir = $imageData->dir;
            
            // Nếu không có dir, thử lấy từ main_path
            if (!$dir && $imageData->main_path) {
                $dir = dirname($imageData->main_path);
            }
        } elseif (is_array($imageData)) {
            $disk = $imageData['disk'] ?? 'public';
            $dir = $imageData['dir'] ?? null;
            
            // Nếu không có dir, thử lấy từ main_path
            if (!$dir && isset($imageData['main_path'])) {
                $dir = dirname($imageData['main_path']);
            }
        }

        if (!$dir || !is_string($dir) || $dir === '') {
            Log::warning("MediaService: Không thể xóa thư mục vì thiếu 'dir'.", ['imageData' => $imageData]);
            return false;
        }

        try {
            if (Storage::disk($disk)->exists($dir)) {
                $deleted = Storage::disk($disk)->deleteDirectory($dir);

                if ($deleted) {
                    Log::info("MediaService: Đã xóa toàn bộ thư mục ảnh: {$dir}");
                    return true;
                } else {
                    Log::warning("MediaService: Không xóa được thư mục: {$dir}");
                    return false;
                }
            } else {
                Log::info("MediaService: Thư mục ảnh đã bị xóa trước đó: {$dir}");
                return true;
            }
        } catch (\Exception $e) {
            Log::error("MediaService: Lỗi khi xóa thư mục {$dir}. Lỗi: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xử lý trọn gói việc cập nhật media cho một model.
     */
    public function updateMedia(
        object $model,
        ?string $originalPath,
        string $directory,
        array $config,
        callable $newImageSetter,
        ?callable $oldImageGetter = null,
        string $logContext = 'media'
    ): bool {
        
        if (empty($originalPath)) {
            return false;
        }

        $newImageData = $this->processAndPrepareData(
            $originalPath,
            $directory,
            $config
        );

        if ($newImageData) {
            $oldImage = $oldImageGetter ? $oldImageGetter() : null;
            $newImageSetter($newImageData);
            
            if ($oldImage) {
                $this->deleteProcessedImages($oldImage);
            }
            return true;
        } 
        
        $modelId = $model->id ?? 'unknown';
        Log::warning("MediaService: Không thể xử lý {$logContext} mới cho model ID {$modelId}. Path gốc: {$originalPath}. (processAndPrepareData thất bại)");
        return false;
    }

    /**
     * Debug method để kiểm tra dữ liệu ảnh
     */
    public function debugImageData(ImageModel|array|null $imageData): void
    {
        if ($imageData instanceof ImageModel) {
            Log::debug('Image Model Data:', [
                'id' => $imageData->id,
                'disk' => $imageData->disk,
                'dir' => $imageData->dir,
                'main_path' => $imageData->main_path,
                'variants' => $imageData->variants,
                'model_type' => get_class($imageData)
            ]);
        } elseif (is_array($imageData)) {
            Log::debug('Image Array Data:', $imageData);
        } else {
            Log::debug('Image Data is null');
        }
    }
}