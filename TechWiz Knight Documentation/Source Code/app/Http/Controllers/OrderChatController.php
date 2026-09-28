<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class OrderChatController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function storeCustomer(Request $request, Order $order)
    {
        abort_unless((int) $order->customer_id === (int) auth()->id(), 403);

        return $this->store($request, $order, 'customer');
    }

    public function storeFarmer(Request $request, Order $order)
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile && (int) $order->farmer_id === (int) $profile->id, 403);

        return $this->store($request, $order, 'farmer');
    }

    public function storeGuest(Request $request, Order $order)
    {
        $allowed = collect(session('ml_guest_orders', []))->map(fn ($id) => (int) $id);
        abort_unless($allowed->contains((int) $order->id), 403);
        abort_unless($order->customer_id === null, 403);

        return $this->store($request, $order, 'guest');
    }

    public function showFarmer(Order $order)
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile && (int) $order->farmer_id === (int) $profile->id, 403);

        $order->load(['items', 'farmer.user', 'market', 'customer', 'messages' => fn ($q) => $q->with('sender')->orderBy('created_at')]);
        $this->markReadFor($order);

        return view('farmer.orders.show', compact('order'));
    }

    private function store(Request $request, Order $order, string $as)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $body = trim($data['body']);
        abort_if($body === '', 422);

        $message = OrderMessage::query()->create([
            'order_id' => $order->id,
            'user_id' => $as === 'guest' ? null : auth()->id(),
            'guest_name' => $as === 'guest' ? ($order->guest_name ?: 'Guest') : null,
            'body' => $body,
            'is_read' => false,
        ]);

        $order->loadMissing('customer', 'farmer.user');

        if (in_array($as, ['customer', 'guest'], true) && $order->farmer?->user) {
            $who = $as === 'guest'
                ? ($order->guest_name ?: 'Guest')
                : auth()->user()->name;
            $this->notifications->send(
                $order->farmer->user,
                'order_chat',
                'New chat · '.$order->order_number,
                $who.': '.mb_strimwidth($body, 0, 120, '…'),
                ['order_id' => $order->id, 'url' => route('farmer.orders.show', $order)]
            );
        }

        if ($as === 'farmer' && $order->customer) {
            $this->notifications->send(
                $order->customer,
                'order_chat',
                'Farmer reply · '.$order->order_number,
                ($order->farmer->stall_name ?? 'Farmer').': '.mb_strimwidth($body, 0, 120, '…'),
                ['order_id' => $order->id, 'url' => route('customer.orders.show', $order)]
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'mine' => true,
                    'sender' => $as === 'guest' ? ($order->guest_name ?: 'You') : auth()->user()->name,
                    'at' => $message->created_at->format('M j, g:i A'),
                ],
            ]);
        }

        $route = match ($as) {
            'farmer' => route('farmer.orders.show', $order),
            'guest' => route('guest.orders.show', $order),
            default => route('customer.orders.show', $order),
        };

        return redirect($route.'#order-chat')->with('success', 'Message sent.');
    }

    private function markReadFor(Order $order): void
    {
        $selfId = auth()->id();
        OrderMessage::query()
            ->where('order_id', $order->id)
            ->where(function ($q) use ($selfId) {
                $q->whereNull('user_id')->orWhere('user_id', '!=', $selfId);
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
