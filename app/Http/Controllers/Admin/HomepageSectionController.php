<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HomepageSectionService;
use App\Models\HomepageSection;
use Illuminate\Http\Request;

class HomepageSectionController extends Controller
{
    protected HomepageSectionService $service;

    public function __construct(HomepageSectionService $service)
    {
        $this->service = $service;
    }

    /**
     * Danh sách các sections
     */
    public function index()
    {
        $sections = $this->service->getAll();
        return view('admin.homepage-sections.index', compact('sections'));
    }

    /**
     * Form chỉnh sửa section
     */
    public function edit(int $id)
    {
        $section = $this->service->getById($id);
        
        if (!$section) {
            return redirect()->route('admin.homepage-sections.index')
                ->with('error', 'Không tìm thấy section.');
        }

        $settingsFields = $this->service->getSettingsFieldsForSection($section->key);

        return view('admin.homepage-sections.edit', compact('section', 'settingsFields'));
    }

    /**
     * Lưu thay đổi section
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'background_image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
        ]);

        // Xử lý checkbox is_active
        $validated['is_active'] = $request->has('is_active');

        $this->service->update($id, $validated);

        return redirect()->route('admin.homepage-sections.index')
            ->with('success', 'Đã cập nhật section thành công!');
    }

    /**
     * Toggle trạng thái active (AJAX)
     */
    public function toggleActive(int $id)
    {
        $section = $this->service->toggleActive($id);

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
            'message' => $section->is_active ? 'Đã bật section' : 'Đã tắt section',
        ]);
    }

    /**
     * Sắp xếp lại thứ tự sections (AJAX)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_sections,id',
        ]);

        $this->service->reorder($request->order);

        return response()->json([
            'success' => true,
            'message' => 'Đã sắp xếp lại thứ tự sections.',
        ]);
    }
}
