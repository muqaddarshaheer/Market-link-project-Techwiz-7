<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->cart()->load('items.product.farmer', 'items.product.market');

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        if (! $product->canPurchase()) {
            return $this->cartFail('This product cannot be ordered right now.', 422);
        }

        if ($data['quantity'] > $product->stock_quantity) {
            return $this->cartFail('Only '.$product->stock_quantity.' left in stock.', 422);
        }

        $cart = $this->cart();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $next = ($item->exists ? $item->quantity : 0) + $data['quantity'];
        if ($next > $product->stock_quantity) {
            return $this->cartFail('That quantity exceeds available stock.', 422);
        }
        $item->quantity = $next;
        $item->save();
        $this->forgetCartCache();

        return $this->cartOk($product->name.' added to your cart.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $item = $this->cart()->items()->where('product_id', $product->id)->firstOrFail();
        if ($data['quantity'] > $product->stock_quantity) {
            return $this->cartFail('Only '.$product->stock_quantity.' available.', 422);
        }
        $item->update(['quantity' => $data['quantity']]);
        $this->forgetCartCache();

        return $this->cartOk('Cart updated.');
    }

    public function remove(Product $product)
    {
        $this->cart()->items()->where('product_id', $product->id)->delete();
        $this->forgetCartCache();

        return $this->cartOk('Item removed.');
    }

    public function guestIndex()
    {
        $lines = $this->guestLines();
        $products = Product::query()->with(['farmer', 'market'])->whereIn('id', array_keys($lines))->get();

        return view('cart.guest', compact('products', 'lines'));
    }

    public function guestAdd(Request $request, Product $product)
    {
        $qty = (int) $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']])['quantity'];
        if (! $product->canPurchase()) {
            return $this->cartFail('This product cannot be ordered right now.', 422);
        }
        $lines = $this->guestLines();
        $next = ($lines[$product->id] ?? 0) + $qty;
        if ($next > $product->stock_quantity) {
            return $this->cartFail('Only '.$product->stock_quantity.' left in stock.', 422);
        }
        $lines[$product->id] = $next;
        session(['ml_guest_cart' => $lines]);

        if ($this->wantsCartJson()) {
            return response()->json([
                'ok' => true,
                'message' => $product->name.' added.',
                'count' => array_sum($lines),
                'cart_url' => route('guest.cart'),
            ]);
        }

        return redirect()->route('guest.cart')->with('success', $product->name.' added.');
    }

    public function guestUpdate(Request $request, Product $product)
    {
        $qty = (int) $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']])['quantity'];
        $lines = $this->guestLines();
        if (! isset($lines[$product->id])) {
            return $this->cartFail('Item not in cart.', 404);
        }
        if ($qty > $product->stock_quantity) {
            return $this->cartFail('Only '.$product->stock_quantity.' available.', 422);
        }
        $lines[$product->id] = $qty;
        session(['ml_guest_cart' => $lines]);

        return $this->guestOk('Cart updated.', $lines);
    }

    public function guestRemove(Product $product)
    {
        $lines = $this->guestLines();
        unset($lines[$product->id]);
        session(['ml_guest_cart' => $lines]);

        return $this->guestOk('Item removed.', $lines);
    }

    private function guestLines(): array
    {
        return array_map('intval', session('ml_guest_cart', []));
    }

    private function cart(): Cart
    {
        return Cart::query()->firstOrCreate(['customer_id' => auth()->id()]);
    }

    private function forgetCartCache(): void
    {
        if (auth()->id()) {
            Cache::forget('user.'.auth()->id().'.cart_qty');
        }
    }

    private function wantsCartJson(): bool
    {
        return request()->expectsJson() || request()->ajax() || request()->boolean('ajax');
    }

    private function cartOk(string $message)
    {
        $cart = $this->cart()->load('items.product');
        if ($this->wantsCartJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'count' => (int) $cart->items->sum('quantity'),
                'total' => $cart->total(),
                'cart_url' => route('cart.index'),
            ]);
        }

        return back()->with('success', $message);
    }

    private function guestOk(string $message, array $lines)
    {
        if ($this->wantsCartJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'count' => array_sum($lines),
                'cart_url' => route('guest.cart'),
            ]);
        }

        return back()->with('success', $message);
    }

    private function cartFail(string $message, int $status = 422)
    {
        if ($this->wantsCartJson()) {
            return response()->json(['ok' => false, 'message' => $message], $status);
        }

        return back()->withErrors(['quantity' => $message]);
    }
}
