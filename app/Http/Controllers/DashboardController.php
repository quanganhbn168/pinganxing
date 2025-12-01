<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CareerApplication;
use App\Models\Product;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function stats()
    {
        // Thống kê tổng quan (Cards)
        $counts = [
            'products' => Product::count(),
            'posts'    => Post::count(),
            'users'    => User::count(),
            'applies'  => CareerApplication::count(),
        ];

        // Thống kê ứng tuyển mới nhất (Table)
        $recentApplies = CareerApplication::with('career:id,name')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'position' => $item->career->name ?? 'N/A',
                'date' => $item->created_at->diffForHumans(),
                'status' => $item->status
            ]);

        // Thống kê biểu đồ (Chart): Số lượng bài viết & Sản phẩm trong 6 tháng gần đây
        $chartData = $this->getChartData();

        return response()->json([
            'counts' => $counts,
            'recent_applies' => $recentApplies,
            'chart' => $chartData
        ]);
    }

    private function getChartData()
    {
        // Giả lập dữ liệu chart cho nhanh, thực tế anh query group by month
        return [
            'labels' => ['T6', 'T7', 'T8', 'T9', 'T10', 'T11'],
            'posts'  => [12, 19, 3, 5, 2, 3],
            'products' => [5, 10, 15, 8, 12, 20],
        ];
    }
    public function toggleField(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'id' => 'required|integer',
            'field' => 'required|string',
        ]);

        $modelClass = $this->resolveModelClass($request->model);
        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Model không tồn tại.'], 404);
        }

        $record = $modelClass::findOrFail($request->id);

        $field = $request->field;

        if (!array_key_exists($field, $record->getAttributes())) {
            return response()->json(['error' => 'Trường không hợp lệ.'], 422);
        }

        $record->$field = !$record->$field;
        $record->save();

        return response()->json([
            'success' => true,
            'value' => $record->$field,
            'message' => "Đã cập nhật $field thành " . ($record->$field ? '✓' : '✗')
        ]);
    }

// Helper nội bộ: resolve tên model từ string
    protected function resolveModelClass($model)
    {
        $model = Str::studly($model);
        return "App\\Models\\{$model}";
    }
}
