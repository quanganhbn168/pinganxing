<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Traits\UploadImageTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage; // Import Storage
use App\Http\Requests\SettingRequest;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingController extends Controller
{
    use UploadImageTrait;

    public function index()
    {
        $setting = Setting::first();
        return view('admin.setting', compact('setting'));
    }

    public function update(SettingRequest $request)
    {
        $setting = Setting::first();
        $data = $request->validated();

        // --- XỬ LÝ ẢNH (Giữ nguyên logic của bạn) ---
        // --- XỬ LÝ ẢNH ---
        // Logo
        if ($request->hasFile('logo')) {
            if ($setting && $setting->logo) $this->deleteImage($setting->logo);
            $data['logo'] = $this->uploadImage($request->file('logo'), 'settings', 512, null, true);
        } elseif ($request->filled('logo')) {
            $data['logo'] = $request->input('logo');
        }

        // Banner
        if ($request->hasFile('banner')) {
            if ($setting && $setting->banner) $this->deleteImage($setting->banner);
            $data['banner'] = $this->uploadImage($request->file('banner'), 'settings', 1920, 300, true, null, false);
        } elseif ($request->filled('banner')) {
            $data['banner'] = $request->input('banner');
        }

        // Meta Image
        if ($request->hasFile('meta_image')) {
            if ($setting && $setting->meta_image) $this->deleteImage($setting->meta_image);
            $data['meta_image'] = $this->uploadImage($request->file('meta_image'), 'settings', 1200, 630, false, null, false);
        } elseif ($request->filled('meta_image')) {
            $data['meta_image'] = $request->input('meta_image');
        }

        if ($request->hasFile('favicon')) {
            $data['favicon'] = $this->generateFavicon($request->file('favicon'));
        }

        // --- XỬ LÝ FILE PROFILE (PDF) ---
        if ($request->hasFile('profile')) {
            // Xoá file cũ nếu có (chỉ xóa nếu nằm trong profiles cũ)
            if ($setting && $setting->profile && !str_starts_with($setting->profile, '/') && Storage::disk('public')->exists($setting->profile)) {
                Storage::disk('public')->delete($setting->profile);
            }
            $data['profile'] = $request->file('profile')->store('profiles', 'public');
        } elseif ($request->filled('profile')) {
             $data['profile'] = $request->input('profile');
        }

        // --- XỬ LÝ VIDEO ---
        $data['video_type'] = $request->input('video_type', 'youtube');

        if ($data['video_type'] === 'upload') {
            if ($request->hasFile('intro_video')) {
                 // Xoá video cũ nếu có (chỉ xóa nếu nằm trong videos cũ)
                if ($setting && $setting->intro_video && !str_starts_with($setting->intro_video, '/') && Storage::disk('public')->exists($setting->intro_video)) {
                    Storage::disk('public')->delete($setting->intro_video);
                }
                $data['intro_video'] = $request->file('intro_video')->store('videos', 'public');
            } elseif ($request->filled('intro_video')) {
                $data['intro_video'] = $request->input('intro_video');
            }
        }

        $setting ? $setting->update($data) : Setting::create($data);

        return redirect()->route('admin.settings.index')->with('success', 'Cập nhật cài đặt thành công.');
    }

    // ... (Giữ nguyên các hàm generateFavicon và tryMakeIcoFromPng) ...
     protected function generateFavicon(UploadedFile $file): string
    {
        $publicFolder = public_path('favicon');

        if (!File::exists($publicFolder)) {
            File::makeDirectory($publicFolder, 0755, true);
        }

        // Danh sách size PNG sẽ xuất
        $sizes = [
            16  => 'favicon-16x16.png',
            32  => 'favicon-32x32.png',
            180 => 'apple-touch-icon.png',
            192 => 'icon-192.png',
            512 => 'icon-512.png',
        ];

        // Xoá file cũ để tránh cache
        foreach ($sizes as $name) {
            $p = $publicFolder . '/' . $name;
            if (File::exists($p)) File::delete($p);
        }
        // Xoá favicon.ico & favicon.png cũ nếu có
        foreach (['favicon.ico', 'favicon.png'] as $old) {
            $p = $publicFolder . '/' . $old;
            if (File::exists($p)) File::delete($p);
        }

        // v3: dùng ImageManager
        $manager = new ImageManager(new Driver());

        // Tạo từng size PNG (cover = scale + crop giữa giống "fit" cũ)
        foreach ($sizes as $size => $filename) {
            $img = $manager->read($file->getRealPath());
            $img->cover($size, $size); // lấp đầy khung, crop trung tâm
            $binary = $img->toPng();   // v3: toPng() không nhận quality
            File::put($publicFolder . '/' . $filename, $binary);
        }

        // Tạo favicon.png (chuẩn 32x32) để fallback
        {
            $img = $manager->read($file->getRealPath());
            $img->cover(32, 32);
            $binary = $img->toPng();
            File::put($publicFolder . '/favicon.png', $binary);
        }

        // Tạo favicon.ico nếu có Imagick (tốt cho trình duyệt cũ / Windows)
        $png32 = $publicFolder . '/favicon.png';
        $ico   = $publicFolder . '/favicon.ico';
        $this->tryMakeIcoFromPng($png32, $ico);

        // Trả về path 32×32 PNG để lưu DB
        return 'favicon/favicon-32x32.png';
    }

    /**
     * Tạo .ico từ .png bằng Imagick nếu có; nếu không có thì bỏ qua.
     */
    private function tryMakeIcoFromPng(string $pngPath, string $icoPath): bool
    {
        if (!File::exists($pngPath)) return false;

        if (class_exists(\Imagick::class)) {
            try {
                $im = new \Imagick();
                $im->readImage($pngPath);
                $im->setImageFormat('ico');
                $im->writeImage($icoPath);
                $im->clear();
                $im->destroy();
                return true;
            } catch (\Throwable $e) {
                // Silent fallback
            }
        }
        return false;
    }
}