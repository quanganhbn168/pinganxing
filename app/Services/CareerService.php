<?php

namespace App\Services;

use App\Models\Career;
use Illuminate\Http\Request;

class CareerService
{
    /**
     * Lấy danh sách có phân trang và tìm kiếm
     */
    public function getLists(Request $request)
    {
        $query = Career::query()->orderBy('position', 'asc')->orderBy('created_at', 'desc');

        if ($request->filled('keyword')) {
            $k = $request->keyword;
            $query->where(function ($q) use ($k) {
                $q->where('name', 'like', "%{$k}%")
                  ->orWhere('location', 'like', "%{$k}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Mặc định 20 item hoặc theo request
        return $query->paginate($request->input('per_page', 20));
    }

    public function create(array $data): Career
    {
        // 1. Map ảnh từ Media Manager
        if (!empty($data['image_original_path'])) {
            $data['image'] = $data['image_original_path'];
        }

        // 2. Boolean
        $data['status']  = isset($data['status']) ? (bool) $data['status'] : false;
        $data['is_home'] = isset($data['is_home']) ? (bool) $data['is_home'] : false;
        
        // 3. Vị trí (Auto max + 1 nếu null)
        if (empty($data['position'])) {
            $data['position'] = Career::max('position') + 1;
        }

        return Career::create($data);
    }

    public function update(Career $career, array $data): bool
    {
        // 1. Map ảnh
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }

        // 2. Boolean checkbox (Cần thiết vì checkbox bỏ chọn sẽ không gửi value)
        $data['status']  = isset($data['status']) ? (bool) $data['status'] : false;
        $data['is_home'] = isset($data['is_home']) ? (bool) $data['is_home'] : false;

        return $career->update($data);
    }

    public function delete($id): bool
    {
        $career = Career::findOrFail($id);
        return $career->delete();
    }
}