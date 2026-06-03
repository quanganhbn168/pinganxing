<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Database\QueryException;
class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        return response()->json($cartItems);
    }
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = (int) $request->input('product_id');
        $variantId = $request->filled('variant_id') ? (int) $request->input('variant_id') : null;
        $quantity = (int) $request->input('quantity');
        $product = Product::query()->findOrFail($productId);

        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->first();
            if (! $variant) {
                return response()->json(['success' => false, 'message' => 'Biến thể không hợp lệ.'], 422);
            }

            if ((float) $variant->price <= 0) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này cần liên hệ báo giá.'], 422);
            }
        } elseif ((float) ($product->price_discount ?: $product->price) <= 0) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm này cần liên hệ báo giá.'], 422);
        }

        if (Auth::check()) {
            $user = Auth::user();

            $query = CartItem::where('user_id', $user->id)->where('product_id', $productId);
            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }

            $cartItem = $query->first();
            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                try {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'product_variant_id' => $variantId,
                        'quantity' => $quantity,
                    ]);
                } catch (QueryException $e) {
                    // Compatibility fallback for old unique key (user_id, product_id).
                    $fallbackItem = CartItem::where('user_id', $user->id)
                        ->where('product_id', $productId)
                        ->first();

                    if (! $fallbackItem) {
                        throw $e;
                    }

                    $fallbackItem->quantity += $quantity;
                    if ($fallbackItem->product_variant_id === null) {
                        $fallbackItem->product_variant_id = $variantId;
                    }
                    $fallbackItem->save();
                }
            }
        } else {
            $cart = session()->get('guest_cart', []);
            $index = $this->findGuestItemIndex($cart, $productId, $variantId);
            if ($index !== null) {
                $cart[$index]['quantity'] = (int) $cart[$index]['quantity'] + $quantity;
            } else {
                $cart[] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                ];
            }
            session(['guest_cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng!',
            'cart'    => $this->getCartDataForAPI()
        ]);
    }
    public function update(Request $request, $cartItemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $quantity = (int) $request->quantity;

        if (Auth::check()) {
            $cartItem = CartItem::where('id', $cartItemId)->where('user_id', Auth::id())->firstOrFail();
            if ($quantity === 0) {
                $cartItem->delete();
                $message = 'Sản phẩm đã được xóa khỏi giỏ!';
            } else {
                $cartItem->update(['quantity' => $quantity]);
                $message = 'Giỏ hàng đã được cập nhật!';
            }
        } else {
            $cart = session()->get('guest_cart', []);
            $index = $this->findGuestItemIndexByKey($cart, (string) $cartItemId);

            if ($index === null) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ.'], 404);
            }

            if ($quantity === 0) {
                unset($cart[$index]);
                $message = 'Sản phẩm đã được xóa khỏi giỏ!';
            } else {
                $cart[$index]['quantity'] = $quantity;
                $message = 'Giỏ hàng đã được cập nhật!';
            }

            session(['guest_cart' => array_values($cart)]);
        }

        return response()->json(['success' => true, 'message' => $message, 'cart' => $this->getCartDataForAPI()]);
    }
    public function remove($cartItemId)
    {
        if (Auth::check()) {
            CartItem::where('id', $cartItemId)->where('user_id', Auth::id())->firstOrFail()->delete();
        } else {
            $cart = session()->get('guest_cart', []);
            $index = $this->findGuestItemIndexByKey($cart, (string) $cartItemId);
            if ($index !== null) {
                unset($cart[$index]);
                session(['guest_cart' => array_values($cart)]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm đã được xóa khỏi giỏ!',
            'cart' => $this->getCartDataForAPI()
        ]);
    }
    private function getCartData()
    {
        if (Auth::check()) {
            return Auth::user()->cartItems()->count();
        }

        $guestCart = session()->get('guest_cart', []);
        return collect($guestCart)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));
    }
    public function showCartPage()
    {
    // Dù có check Auth hay không, chúng ta luôn cần biến $cartItems trong view.
        $cartData = $this->getCartDataForAPI();
        $cartItems = $cartData['items'];
        
    // Truyền biến $cartItems vào view.
        return view('cart.index', ['cartItems' => $cartItems]);
    }

    public function summary()
    {
        return response()->json([
            'success' => true,
            'cart' => $this->getCartDataForAPI(),
        ]);
    }
    public function buyNow(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required','integer', /* Rule::exists('products','id') */],
            'variant_id'    => ['nullable','integer', /* Rule::exists('product_variants','id') */],
            'quantity'      => ['required','integer','min:1'],
            'variant_text'  => ['nullable','string','max:255'],
        ]);

        $productId   = (int) $data['product_id'];
        $variantId   = $data['variant_id'] ? (int) $data['variant_id'] : null;
        $qty         = (int) $data['quantity'];
        $variantText = $data['variant_text'] ?? null;
        $product = Product::query()->findOrFail($productId);

        // (Khuyến nghị) Nếu có Model, kiểm tra biến thể thuộc đúng product:
        if ($variantId) {
            $variant = \App\Models\ProductVariant::where('id', $variantId)
                    ->where('product_id', $productId)
                    ->first();
            if (!$variant) {
                return back()->withErrors(['variant_id' => 'Biến thể không hợp lệ với sản phẩm này.']);
            }

            if ((float) $variant->price <= 0) {
                return back()->withErrors(['product_id' => 'Sản phẩm này cần liên hệ báo giá.']);
            }
        } elseif ((float) ($product->price_discount ?: $product->price) <= 0) {
            return back()->withErrors(['product_id' => 'Sản phẩm này cần liên hệ báo giá.']);
        }

        if (Auth::check()) {
            // --- USER ĐĂNG NHẬP ---
            $user = Auth::user();

            $query = $user->cartItems()->where('product_id', $productId);
            if ($variantId) {
                $query->where('product_variant_id', $variantId);
            } else {
                $query->whereNull('product_variant_id');
            }
            $item = $query->firstOrNew();

            $currentQty = max(0, (int)($item->quantity ?? 0));
            $item->quantity = $currentQty + $qty;
            $item->product_id = $productId;
            $item->product_variant_id = $variantId;

            $item->save();

            // (Tuỳ chọn) set "đã chọn để checkout" nếu anh muốn checkout chỉ lấy item vừa bấm:
            // session(['checkout_selection' => [ ['product_id'=>$productId,'variant_id'=>$variantId] ]]);

        } else {
            // --- KHÁCH (GUEST) ---
            $cart = session()->get('guest_cart', []);
            $foundIndex = $this->findGuestItemIndex($cart, $productId, $variantId);

            if ($foundIndex !== null) {
                $cart[$foundIndex]['quantity'] = max(0, (int)$cart[$foundIndex]['quantity']) + $qty;
            } else {
                $cart[] = [
                    'product_id'   => $productId,
                    'variant_id'   => $variantId,
                    'quantity'     => $qty,
                ];
            }

            session(['guest_cart' => $cart]);

            // (Tuỳ chọn) nếu muốn checkout chỉ item vừa bấm:
            // session(['checkout_selection' => [ ['product_id'=>$productId,'variant_id'=>$variantId] ]]);
        }

        return redirect()->route('checkout.index');
    }

    private function getCartDataForAPI()
    {
        if (Auth::check()) {
            $items = $this->getAuthCartItems();
        } else {
            $items = $this->getGuestCartItems();
        }

        $total_price = 0;
        $total_quantity = 0;
        $normalized = collect($items)->map(function ($item) use (&$total_price, &$total_quantity) {
            $price = (float) ($item['price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            $total_price += $quantity * $price;
            $total_quantity += $quantity;
            return $item;
        })->values();

        return [
            'items'          => $normalized,
            'total_price'    => $total_price,
            'total_quantity' => $total_quantity,
        ];
    }

    public function merge(Request $request)
    {
        $guestCart = $request->input('guest_cart', []);
        $user = auth()->user();

        if ($user && !empty($guestCart)) {
            foreach ($guestCart as $guestItem) {
                $productId = (int) ($guestItem['product_id'] ?? 0);
                $variantId = isset($guestItem['variant_id']) ? (int) $guestItem['variant_id'] : null;
                $quantity = max(1, (int) ($guestItem['quantity'] ?? 1));

                if ($productId <= 0) {
                    continue;
                }

                $query = CartItem::where('user_id', $user->id)->where('product_id', $productId);
                if ($variantId) {
                    $query->where('product_variant_id', $variantId);
                } else {
                    $query->whereNull('product_variant_id');
                }
                $existingItem = $query->first();

                if ($existingItem) {
                    $existingItem->quantity += $quantity;
                    $existingItem->save();
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'product_variant_id' => $variantId,
                        'quantity' => $quantity,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Giỏ hàng đã được gộp.']);
    }

    private function getAuthCartItems(): array
    {
        $user = auth()->user();
        if (! $user) {
            return [];
        }

        $cartItems = CartItem::where('user_id', $user->id)->with('product.slug', 'product.image', 'variant.image')->get();

        return $cartItems->map(function (CartItem $item) {
            $product = $item->product;
            if (! $product) {
                return null;
            }

            $variant = $item->variant;
            $variantText = $variant ? $this->resolveVariantText($variant) : null;
            $price = $variant && $variant->price !== null
                ? (float) $variant->price
                : (float) ($product->price_discount ?: $product->price);

            $imagePath = $variant?->image ?: $product->image;

            return [
                'id' => (string) $item->id,
                'product_id' => (int) $product->id,
                'variant_id' => $variant?->id,
                'name' => (string) $product->name,
                'price' => $price,
                'quantity' => (int) $item->quantity,
                'image' => $this->resolveImageUrl($imagePath),
                'slug' => $product->slug?->slug,
                'url' => $product->slug?->slug ? url('/san-pham/' . $product->slug->slug) : '#',
                'product_type' => (string) ($product->type ?? 'simple'),
                'variant_text' => $variantText,
            ];
        })->filter()->values()->toArray();
    }

    private function getGuestCartItems(): array
    {
        $guestCart = session()->get('guest_cart', []);
        if (empty($guestCart)) {
            return [];
        }

        $productIds = collect($guestCart)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $variantIds = collect($guestCart)->pluck('variant_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $products = Product::with('slug', 'image')->whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::with('image')->whereIn('id', $variantIds)->get()->keyBy('id');

        return collect($guestCart)->map(function ($row) use ($products, $variants) {
            $productId = (int) ($row['product_id'] ?? 0);
            $variantId = isset($row['variant_id']) ? (int) $row['variant_id'] : null;
            $quantity = max(1, (int) ($row['quantity'] ?? 1));

            $product = $products->get($productId);
            if (! $product) {
                return null;
            }

            $variant = $variantId ? $variants->get($variantId) : null;
            $variantText = $variant ? $this->resolveVariantText($variant) : null;
            $price = $variant && $variant->price !== null
                ? (float) $variant->price
                : (float) ($product->price_discount ?: $product->price);

            return [
                'id' => $this->makeGuestKey($productId, $variantId),
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => (string) $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image' => $this->resolveImageUrl($variant?->image ?: $product->image),
                'slug' => $product->slug?->slug,
                'url' => $product->slug?->slug ? url('/san-pham/' . $product->slug->slug) : '#',
                'product_type' => (string) ($product->type ?? 'simple'),
                'variant_text' => $variantText,
            ];
        })->filter()->values()->toArray();
    }

    private function resolveVariantText(ProductVariant $variant): ?string
    {
        $options = (array) ($variant->options ?? []);
        if (empty($options)) {
            return null;
        }

        $attributeIds = collect(array_keys($options))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $attributes = Attribute::whereIn('id', $attributeIds)->get()->keyBy('id');
        $parts = [];
        foreach ($options as $attributeId => $value) {
            $name = $attributes->get((int) $attributeId)?->name;
            if (! $name || ! filled($value)) {
                continue;
            }
            $parts[] = $name . ': ' . $value;
        }

        return empty($parts) ? null : implode(', ', $parts);
    }

    private function makeGuestKey(int $productId, ?int $variantId): string
    {
        return $productId . '-' . ($variantId ?: '0');
    }

    private function findGuestItemIndex(array $cart, int $productId, ?int $variantId): ?int
    {
        foreach ($cart as $index => $item) {
            $pid = (int) ($item['product_id'] ?? 0);
            $vid = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            if ($pid === $productId && $vid === $variantId) {
                return $index;
            }
        }

        return null;
    }

    private function findGuestItemIndexByKey(array $cart, string $key): ?int
    {
        foreach ($cart as $index => $item) {
            $pid = (int) ($item['product_id'] ?? 0);
            $vid = isset($item['variant_id']) ? (int) $item['variant_id'] : null;
            if ($this->makeGuestKey($pid, $vid) === $key) {
                return $index;
            }
        }

        return null;
    }

    private function resolveImageUrl(mixed $image): string
    {
        $fallback = asset('images/setting/no-image.png');

        if (is_string($image) && $image !== '') {
            return asset($image);
        }

        if (is_object($image)) {
            if (isset($image->url) && is_string($image->url) && filled($image->url)) {
                return (string) $image->url;
            }

            if (isset($image->path) && is_string($image->path) && filled($image->path)) {
                return asset($image->path);
            }

            if (method_exists($image, 'url')) {
                $url = $image->url();
                if (is_string($url) && filled($url)) {
                    return $url;
                }
            }
        }

        return $fallback;
    }
}
