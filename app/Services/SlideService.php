<?php

namespace App\Services;

use App\Models\Slide;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SlideService
{
    use UploadImageTrait;

    /* ---------- CREATE ---------- */
    public function create(Request $request): Slide
    {
        $data = $request->validate($this->rules());

        // Chuẩn hoá boolean khi checkbox không gửi lên
        $data['status'] = (bool) ($data['status'] ?? false);
        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        // Upload theo từng type
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadByType($request, (int) $data['type']);
        }

        return Slide::create($data);
    }

    /* ---------- UPDATE ---------- */
    public function update(Request $request, Slide $slide): Slide
    {
        $data = $request->validate($this->rules());

        $data['status'] = (bool) ($data['status'] ?? false);
        $data['is_home'] = (bool) ($data['is_home'] ?? false);

        // Chỉ xoá ảnh cũ khi có upload ảnh mới
        if ($request->hasFile('image')) {
            $this->deleteSlideImage($slide);
            $data['image'] = $this->uploadByType($request, (int) $data['type']);
        } else {
            // Không upload mới -> giữ nguyên ảnh cũ
            unset($data['image']);
        }

        $slide->update($data);
        return $slide;
    }

    /* ---------- DELETE ---------- */
    public function delete(Slide $slide): void
    {
        $this->deleteSlideImage($slide);
        $slide->delete();
    }

    /* ---------- RULES ---------- */
    private function rules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'link'     => 'nullable|url|max:255',
            'position' => 'nullable|integer',
            'status'   => 'nullable|boolean',
            'is_home'  => 'nullable|boolean',
            'type'     => ['required', 'integer', Rule::in([
                Slide::TYPE_SLIDE,
                Slide::TYPE_PARTNER,
                Slide::TYPE_POPUP,
                Slide::TYPE_ADVERTISEMENT,
            ])],
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

    /* ---------- HELPERS ---------- */
    private function uploadByType(Request $request, int $type): string
    {
        // Tuỳ loại slide mà resize khác nhau (giữ đúng logic bạn đang dùng)
        return match ($type) {
            Slide::TYPE_PARTNER       => $this->uploadImage($request->file('image'), 'uploads/slides', 350, 60, true),
            Slide::TYPE_POPUP         => $this->uploadImage($request->file('image'), 'uploads/slides', 800, 1000, true),
            Slide::TYPE_ADVERTISEMENT => $this->uploadImage($request->file('image'), 'uploads/slides', 500, 250, true),
            default                   => $this->uploadImage($request->file('image'), 'uploads/slides', 1920, 640, false),
        };
    }

    private function deleteSlideImage(Slide $slide): void
    {
        if ($slide->image) {
            $this->deleteImage($slide->image);
        }
    }
}
