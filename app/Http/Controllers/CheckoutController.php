<?php
namespace App\Http\Controllers;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Order; 
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
class CheckoutController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * Hiển thị trang thanh toán.
     */
    public function index()
    {
        $cartItems = collect([]);
        if (Auth::guard('web')->check()) {
            $cartItems = Auth::guard('web')->user()->cartItems()->with('product', 'variant')->get();
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.page')->with('error', 'Giỏ hàng của bạn đang trống!');
            }
        } else {
            $cartItems = collect($this->getGuestCheckoutItems());
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.page')->with('error', 'Giỏ hàng của bạn đang trống!');
            }
        }
        return view('checkout.index', [
            'cartItems'  => $cartItems,
            'bankTransferEnabled' => $this->bankTransferEnabled(),
        ]);
    }
    /**
     * Xử lý việc đặt hàng.
     */
    public function placeOrder(Request $request)
    {
        $customerData = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:15',
            'customer_address' => 'required|string|max:255',
            'payment_method'   => ['required', Rule::in($this->bankTransferEnabled() ? ['cod', 'bank_transfer'] : ['cod'])],
            'note'             => 'nullable|string',
            'cart_data'        => 'nullable|json', 
        ]);
        try {
            $user = Auth::guard('web')->user();
            $cartItems = $user ? $user->cartItems()->with('product', 'variant')->get() : collect([]);
            $guestCart = !$user ? session()->get('guest_cart', []) : [];
            $order = $this->orderService->createFromCheckout($customerData, $cartItems, $guestCart);
            if (! $user) {
                session()->forget('guest_cart');
            }
            return redirect()->route('thank-you')
                ->with('clear_guest_cart', true)
                ->with('order_id', $order->id)
                ->with('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Đặt hàng thất bại, vui lòng thử lại sau ít phút.')->withInput();
        }
    }
    private function getGuestCheckoutItems(): array
    {
        $guestCart = session()->get('guest_cart', []);
        if (empty($guestCart)) {
            return [];
        }

        $productIds = collect($guestCart)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $variantIds = collect($guestCart)->pluck('variant_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
        $attributes = Attribute::whereIn(
            'id',
            $variants->flatMap(fn (ProductVariant $variant) => array_keys((array) ($variant->options ?? [])))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values()
        )->get()->keyBy('id');

        return collect($guestCart)->map(function ($row) use ($products, $variants, $attributes) {
            $productId = (int) ($row['product_id'] ?? 0);
            $variantId = isset($row['variant_id']) ? (int) $row['variant_id'] : null;
            $quantity = max(1, (int) ($row['quantity'] ?? 1));

            $product = $products->get($productId);
            if (! $product) {
                return null;
            }

            $variant = $variantId ? $variants->get($variantId) : null;
            $variantText = null;
            if ($variant) {
                $parts = [];
                foreach ((array) ($variant->options ?? []) as $attributeId => $value) {
                    $name = $attributes->get((int) $attributeId)?->name;
                    if ($name && filled($value)) {
                        $parts[] = $name . ': ' . $value;
                    }
                }
                $variantText = empty($parts) ? null : implode(', ', $parts);
            }

            return [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $variant && $variant->price !== null ? (float) $variant->price : (float) ($product->price_discount ?: $product->price),
                'variant_text' => $variantText,
            ];
        })->filter()->values()->toArray();
    }

    private function bankTransferEnabled(): bool
    {
        return collect(['bank_id', 'account_no', 'account_name'])
            ->every(fn (string $key): bool => filled(config("vietqr.{$key}")));
    }
}
