<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function index(WhatsAppMessage $whatsAppMessage): View
    {
        $orders = Order::query()
            ->with(['items.product', 'items.size', 'items.color', 'review'])
            ->latest()
            ->get();

        return view('private.orders.index', [
            'orders' => $orders,
            'reviewInviteUrls' => $orders->mapWithKeys(fn (Order $order): array => [
                $order->id => $whatsAppMessage->url($whatsAppMessage->reviewInvite($order), $order->phone),
            ]),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ])],
        ]);

        $order->update($data);

        return back()->with('status', 'Estado del pedido actualizado.');
    }
}
