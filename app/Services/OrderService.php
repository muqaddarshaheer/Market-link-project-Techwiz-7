<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Split the cart into one pre-order per farmer + market and reserve stock.
     */
    public function placeFromCart(User $customer, Cart $cart, string $pickupDate, string $pickupSlot, ?string $note): array
    {
        $cart->load('items.product.farmer', 'items.product.market');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $groups = $cart->items->groupBy(fn ($item) => $item->product->farmer_id.'-'.$item->product->market_id);
        $orders = [];

        DB::transaction(function () use ($groups, $customer, $pickupDate, $pickupSlot, $note, $cart, &$orders) {
            foreach ($groups as $items) {
                $first = $items->first()->product;
                $farmer = $first->farmer;
                $cutoff = $this->cutoffFor($farmer->cutoff_hours ?? 12, $pickupDate, $pickupSlot);

                if (now()->gte($cutoff)) {
                    throw ValidationException::withMessages([
                        'pickup_date' => 'That pickup window is already past the cutoff. Choose a later date or slot.',
                    ]);
                }

                $order = Order::query()->create([
                    'order_number' => 'ML-'.now()->year.'-'.str_pad((string) ((int) Order::max('id') + 1), 6, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'farmer_id' => $farmer->id,
                    'market_id' => $first->market_id,
                    'pickup_date' => $pickupDate,
                    'pickup_slot' => $pickupSlot,
                    'status' => 'placed',
                    'total_amount' => 0,
                    'customer_note' => $note,
                    'cutoff_time' => $cutoff,
                ]);

                $total = 0;
                foreach ($items as $item) {
                    $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);
                    if (! $product->canPurchase() || $product->stock_quantity < $item->quantity) {
                        throw ValidationException::withMessages([
                            'cart' => $product->name.' does not have enough stock.',
                        ]);
                    }
                    $product->decrement('stock_quantity', $item->quantity);
                    if ($product->stock_quantity <= 0) {
                        $product->update(['is_sold_out' => true]);
                    }
                    $subtotal = $item->quantity * (float) $product->price;
                    $total += $subtotal;
                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->update(['total_amount' => $total]);
                $orders[] = $order->fresh(['items', 'farmer.user', 'market']);
            }

            $cart->items()->delete();
        });

        foreach ($orders as $order) {
            $this->notifications->send(
                $customer,
                'order_placed',
                'Pre-order placed',
                'Order '.$order->order_number.' is waiting for '.$order->farmer->stall_name.'.',
                ['order_id' => $order->id]
            );
            if ($order->farmer->user) {
                $this->notifications->send(
                    $order->farmer->user,
                    'new_order',
                    'New pre-order',
                    $customer->name.' placed '.$order->order_number.' for pickup on '.$order->pickup_date->format('M j').'.',
                    ['order_id' => $order->id]
                );
            }
        }

        return $orders;
    }

    public function restoreStock(Order $order): void
    {
        $order->load('items.product');
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock_quantity', $item->quantity);
                if ($item->product->stock_quantity > 0) {
                    $item->product->update(['is_sold_out' => false]);
                }
            }
        }
    }

    public function cutoffFor(int $hours, string $date, string $slot): \Carbon\Carbon
    {
        $start = '09:00';
        if (preg_match('/(\d{1,2}:\d{2})/', $slot, $m)) {
            $start = $m[1];
        }

        return \Carbon\Carbon::parse($date.' '.$start)->subHours($hours);
    }
}
