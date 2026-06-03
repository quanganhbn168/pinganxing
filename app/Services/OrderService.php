<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCheckout(array $customerData, EloquentCollection|Collection $cartItems, array $guestCart = []): Order
    {
        return DB::transaction(function () use ($customerData, $cartItems, $guestCart): Order {
            $items = $cartItems->isNotEmpty()
                ? $this->normalizeAuthItems($cartItems)
                : $this->normalizeGuestItems($guestCart);

            if ($items->isEmpty()) {
                throw new \RuntimeException('Giỏ hàng của bạn đang trống.');
            }

            $total = $items->sum('subtotal');

            $order = Order::query()->create([
                'user_id' => auth('web')->id(),
                'customer_name' => $customerData['customer_name'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_address' => $customerData['customer_address'],
                'payment_method' => $customerData['payment_method'],
                'note' => $customerData['note'] ?? null,
                'total_price' => $total,
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $item['product_name'],
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($cartItems->isNotEmpty()) {
                CartItem::query()->whereKey($cartItems->pluck('id'))->delete();
            }

            return $order;
        });
    }

    private function normalizeAuthItems(EloquentCollection|Collection $cartItems): Collection
    {
        if (method_exists($cartItems, 'loadMissing')) {
            $cartItems->loadMissing('product', 'variant');
        }

        return $cartItems
            ->map(fn (CartItem $item): ?array => $this->normalizeItem(
                $item->product,
                $item->variant,
                (int) $item->quantity,
            ))
            ->filter()
            ->values();
    }

    private function normalizeGuestItems(array $guestCart): Collection
    {
        if (empty($guestCart)) {
            return collect();
        }

        $productIds = collect($guestCart)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();
        $variantIds = collect($guestCart)->pluck('variant_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();

        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');
        $variants = ProductVariant::query()->whereIn('id', $variantIds)->get()->keyBy('id');

        return collect($guestCart)
            ->map(function (array $row) use ($products, $variants): ?array {
                $productId = (int) ($row['product_id'] ?? 0);
                $variantId = isset($row['variant_id']) ? (int) $row['variant_id'] : null;
                $quantity = max(1, (int) ($row['quantity'] ?? 1));

                return $this->normalizeItem(
                    $products->get($productId),
                    $variantId ? $variants->get($variantId) : null,
                    $quantity,
                );
            })
            ->filter()
            ->values();
    }

    private function normalizeItem(?Product $product, ?ProductVariant $variant, int $quantity): ?array
    {
        if (! $product || $quantity < 1) {
            return null;
        }

        if ($variant && (int) $variant->product_id !== (int) $product->id) {
            return null;
        }

        $price = $variant
            ? (float) $variant->price
            : (float) ($product->price_discount ?: $product->price);

        if ($price <= 0) {
            throw new \RuntimeException('Sản phẩm ' . $product->name . ' cần liên hệ báo giá.');
        }

        return [
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'product_name' => $variant ? $product->name . ' - ' . ($variant->sku ?: 'Biến thể') : $product->name,
            'product_price' => $price,
            'quantity' => $quantity,
            'subtotal' => $price * $quantity,
        ];
    }
}
