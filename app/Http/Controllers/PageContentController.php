<?php

namespace App\Http\Controllers;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageContentController extends Controller
{
    public function update(Request $request)
    {
        // Thêm middleware để đảm bảo chỉ admin mới có quyền thực hiện
        // ví dụ: $this->middleware('is_admin');

        $data = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'present', // 'present' cho phép giá trị là chuỗi rỗng
        ]);

        $key = $data['key'];
        $value = $data['value'] ?? ''; // Đảm bảo value không bị null

        // Dùng updateOrCreate để tự động tạo nếu key chưa tồn tại
        PageContent::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Xóa cache cũ của key này đi để hệ thống nhận giá trị mới
        Cache::forget('page_content.' . $key);

        return response()->json(['success' => true, 'message' => 'Cập nhật thành công.']);
    }
}
