<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

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
        abort_unless($product->canPurchase(), 422, 'This product cannot be ordered right now.');

        if ($data['quantity'] > $product->stock_quantity) {
            return back()->withErrors(['quantity' => 'Only '.$product->stock_quantity.' left in stock.']);
        }

        $cart = $this->cart();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $next = ($item->exists ? $item->quantity : 0) + $data['quantity'];
        if ($next > $product->stock_quantity) {
            return back()->withErrors(['quantity' => 'That quantity exceeds available stock.']);
        }
        $item->quantity = $next;
        $item->save();

        return back()->with('success', $product->name.' added to your cart.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $item = $this->cart()->items()->where('product_id', $product->id)->firstOrFail();
        if ($data['quantity'] > $product->stock_quantity) {
            return back()->withErrors(['quantity' => 'Only '.$product->stock_quantity.' available.']);
        }
        $item->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Product $product)
    {
        $this->cart()->items()->where('product_id', $product->id)->delete();

        return back()->with('success', 'Item removed.');
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
        abort_unless($product->canPurchase(), 422);
        $lines = $this->guestLines();
        $next = ($lines[$product->id] ?? 0) + $qty;
        if ($next > $product->stock_quantity) {
            return back()->withErrors(['quantity' => 'Only '.$product->stock_quantity.' left in stock.']);
        }
        $lines[$product->id] = $next;
        session(['ml_guest_cart' => $lines]);

        return redirect()->route('guest.cart')->with('success', $product->name.' added.');
    }

    public function guestRemove(Product $product)
    {
        $lines = $this->guestLines();
        unset($lines[$product->id]);
        session(['ml_guest_cart' => $lines]);

        return back()->with('success', 'Item removed.');
    }

    private function guestLines(): array
    {
        return array_map('intval', session('ml_guest_cart', []));
    }

    private function cart(): Cart
    {
        return Cart::query()->firstOrCreate(['customer_id' => auth()->id()]);
    }
}
