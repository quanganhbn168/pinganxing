<?php

namespace App\Services;

use App\Models\HomepageSection;
use Illuminate\Support\Collection;

class HomepageSectionService
{
    /**
     * Lấy tất cả sections
     */
    public function getAll(): Collection
    {
        return HomepageSection::ordered()->get();
    }

    /**
     * Lấy sections đang active (cho frontend)
     */
    public function getActive(): Collection
    {
        return HomepageSection::active()->ordered()->get();
    }

    /**
     * Lấy sections active và keyBy 'key' để dễ truy cập
     */
    public function getActiveKeyedByKey(): Collection
    {
        return $this->getActive()->keyBy('key');
    }

    /**
     * Lấy 1 section theo key
     */
    public function getByKey(string $key): ?HomepageSection
    {
        return HomepageSection::where('key', $key)->first();
    }

    /**
     * Lấy 1 section theo ID
     */
    public function getById(int $id): ?HomepageSection
    {
        return HomepageSection::find($id);
    }

    /**
     * Cập nhật section
     */
    public function update(int $id, array $data): HomepageSection
    {
        $section = HomepageSection::findOrFail($id);
        
        // Xử lý settings nếu có
        if (isset($data['settings']) && is_array($data['settings'])) {
            // Merge với settings hiện tại để không mất data
            $data['settings'] = array_merge(
                $section->settings ?? [],
                $data['settings']
            );
        }

        $section->update($data);
        return $section->fresh();
    }

    /**
     * Toggle trạng thái active
     */
    public function toggleActive(int $id): HomepageSection
    {
        $section = HomepageSection::findOrFail($id);
        $section->is_active = !$section->is_active;
        $section->save();
        return $section;
    }

    /**
     * Sắp xếp lại thứ tự sections
     * @param array $orderedIds Array các ID theo thứ tự mới
     */
    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $order => $id) {
            HomepageSection::where('id', $id)->update(['order' => $order]);
        }
    }

    /**
     * Lấy config fields cho từng loại section
     * Được dùng để render form edit trong admin
     */
    public function getSettingsFieldsForSection(string $key): array
    {
        $fieldsMap = [
            'hero' => [
                // Hero slider không cần settings thêm, lấy từ Slides model
            ],
            'intro' => [
                ['name' => 'button_text', 'label' => 'Text nút 1', 'type' => 'text'],
                ['name' => 'button_link', 'label' => 'Link nút 1', 'type' => 'text'],
                ['name' => 'button_2_text', 'label' => 'Text nút 2', 'type' => 'text'],
                ['name' => 'button_2_link', 'label' => 'Link nút 2', 'type' => 'text'],
            ],
            'fields' => [
                // Lĩnh vực lấy từ FieldCategory model
            ],
            'projects' => [
                // Dự án lấy từ Project model
            ],
            'partners' => [
                ['name' => 'quote_text', 'label' => 'Nội dung trích dẫn', 'type' => 'textarea'],
                ['name' => 'quote_author', 'label' => 'Tên tác giả', 'type' => 'text'],
                ['name' => 'quote_position', 'label' => 'Chức vụ', 'type' => 'text'],
                ['name' => 'quote_image', 'label' => 'Ảnh minh họa trích dẫn', 'type' => 'image'],
            ],
            'core_values' => [
                // Banner lấy từ Slides model (type=BANNER_AD)
            ],
            'news' => [
                // Tin tức lấy từ Post model
            ],
            'careers' => [
                ['name' => 'card_1_title', 'label' => 'Tiêu đề Card 1', 'type' => 'text'],
                ['name' => 'card_1_link', 'label' => 'Link Card 1', 'type' => 'text'],
                ['name' => 'card_1_button', 'label' => 'Text nút Card 1', 'type' => 'text'],
                ['name' => 'card_2_title', 'label' => 'Tiêu đề Card 2', 'type' => 'text'],
                ['name' => 'card_2_link', 'label' => 'Link Card 2', 'type' => 'text'],
                ['name' => 'card_2_button', 'label' => 'Text nút Card 2', 'type' => 'text'],
                ['name' => 'card_3_title', 'label' => 'Tiêu đề Card 3', 'type' => 'text'],
                ['name' => 'card_3_link', 'label' => 'Link Card 3', 'type' => 'text'],
                ['name' => 'card_3_button', 'label' => 'Text nút Card 3', 'type' => 'text'],
            ],
            'testimonials' => [
                // Đánh giá lấy từ Testimonial model
            ],
            'contact_form' => [
                ['name' => 'feature_1_icon', 'label' => 'Icon Feature 1', 'type' => 'text', 'placeholder' => 'fa-solid fa-gears'],
                ['name' => 'feature_1_text', 'label' => 'Text Feature 1', 'type' => 'text'],
                ['name' => 'feature_2_icon', 'label' => 'Icon Feature 2', 'type' => 'text', 'placeholder' => 'fa-solid fa-headset'],
                ['name' => 'feature_2_text', 'label' => 'Text Feature 2', 'type' => 'text'],
                ['name' => 'feature_3_icon', 'label' => 'Icon Feature 3', 'type' => 'text', 'placeholder' => 'fa-solid fa-tags'],
                ['name' => 'feature_3_text', 'label' => 'Text Feature 3', 'type' => 'text'],
            ],
        ];

        return $fieldsMap[$key] ?? [];
    }
}
