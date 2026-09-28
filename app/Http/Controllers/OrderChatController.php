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

    public function showFarmer(Order $order)
    {
        $profile = auth()->user()->farmerProfile;
        abort_unless($profile && (int) $order->farmer_id === (int) $profile->id, 403);

        $order->load(['items', 'farmer.user', 'market', 'customer', 'messages' => fn ($q) => $q->with('sender')->orderBy('created_at')]);
        $this->markReadFor($order, 'farmer');

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
            'user_id' => auth()->id(),
            'body' => $body,
            'is_read' => false,
        ]);

        $order->loadMissing('customer', 'farmer.user');

        if ($as === 'customer' && $order->farmer?->user) {
            $this->notifications->send(
                $order->farmer->user,
                'order_chat',
                'New chat · '.$order->order_number,
                auth()->user()->name.': '.mb_strimwidth($body, 0, 120, '…'),
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
                    'sender' => auth()->user()->name,
                    'at' => $message->created_at->format('M j, g:i A'),
                ],
            ]);
        }

        $route = $as === 'farmer'
            ? route('farmer.orders.show', $order)
            : route('customer.orders.show', $order);

        return redirect($route.'#order-chat')->with('success', 'Message sent.');
    }

    private function markReadFor(Order $order, string $as): void
    {
        $selfId = auth()->id();
        OrderMessage::query()
            ->where('order_id', $order->id)
            ->where('user_id', '!=', $selfId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
