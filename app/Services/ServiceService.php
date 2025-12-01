<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Handlers\ImageGalleryHandler;
use App\Traits\UploadImageTrait;
class ServiceService
{
    use UploadImageTrait;
    public function __construct(
        protected ImageGalleryHandler $imageGallery
    ) {}

    public function create(Request $request): Service
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'nullable|boolean',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['image'] = $this->uploadImage($request->file('image'), 'uploads/services', 800, 800, true);

        if ($request->hasFile('banner')) {
            $data['banner'] = $this->uploadImage($request->file('banner'), 'uploads/services', 1920,800, true);
        }

        $service = Service::create($data);


        return $service;
    }

    public function update(Request $request, Service $service): Service
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        // Slug tự sinh nếu không có
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Cập nhật ảnh đại diện
        if ($request->hasFile('image')) {
            $this->deleteImage($service->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'uploads/services', 800, 800, true);
        }

        // Cập nhật banner
        if ($request->hasFile('banner')) {
            $this->deleteImage($service->banner);
            $data['banner'] = $this->uploadImage($request->file('banner'), 'uploads/services', 1920,800, true);
        }

        // Cập nhật thông tin cơ bản
        $service->update($data);


        return $service;
    }

    public function delete(Service $service): void
    {
        $this->deleteImage($service->image);
        $this->deleteImage($service->banner);


        $service->delete();
    }

    public function getAll()
    {
        $slides = Slide::where('status', 1)->get();
        return $slides;
    }
}
