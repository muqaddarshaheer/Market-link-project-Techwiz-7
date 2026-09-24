<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getOrCreateCart()->load(['items.product.farmer', 'items.product.market']);

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $product = Product::with('farmer')->findOrFail($data['product_id']);

        if (! $product->farmer->isApproved() || ! $product->isInStock()) {
            return back()->with('error', 'This product is not available.');
        }

        if ($data['quantity'] > $product->stock_quantity) {
            return back()->with('error', 'Only '.$product->stock_quantity.' '.$product->unit.' available in stock.');
        }

        $cart = $this->getOrCreateCart();
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $data['quantity'];
            if ($newQty > $product->stock_quantity) {
                return back()->with('error', 'Cannot add more — exceeds available stock.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
            ]);
        }

        return back()->with('success', $product->name.' added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeCartItem($item);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        $product = $item->product;
        if ($data['quantity'] > $product->stock_quantity) {
            return back()->with('error', 'Only '.$product->stock_quantity.' available.');
        }

        $item->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $item)
    {
        $this->authorizeCartItem($item);
        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();

        return back()->with('success', 'Cart cleared.');
    }

    protected function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(['customer_id' => auth()->id()]);
    }

    protected function authorizeCartItem(CartItem $item): void
    {
        abort_unless($item->cart->customer_id === auth()->id(), 403);
    }
}
