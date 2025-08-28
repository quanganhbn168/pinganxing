<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name']);
        // Thống nhất: status = 1 (boolean/int) hoặc nếu bạn dùng enum/string thì thống nhất lại ở cả 2 nơi
        $products = Product::where('status', 1)->orderBy('name')->get(['id','name','price','compare_at_price']);
        return view('admin.orders.create', compact('users', 'products'));
    }

    public function store(OrderRequest $request)
    {
        try {
            $this->orderService->createForAdmin($request->validated());
            return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        // Chỉ load những quan hệ chắc chắn tồn tại
        $order->load(['orderItems.product', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $users = User::orderBy('name')->get(['id','name']);
        $products = Product::where('status', 1)->orderBy('name')->get(['id','name','price','compare_at_price']);
        $order->load('orderItems');
        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    public function update(OrderRequest $request, Order $order)
    {
        try {
            $this->orderService->updateForAdmin($order, $request->validated());
            return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công.');
    }
}
