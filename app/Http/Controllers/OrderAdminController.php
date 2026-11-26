<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsappAutomationService;
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

    public function update(Request $request, Order $order, WhatsappAutomationService $automation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([
                Order::STATUS_PENDING,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ])],
        ]);

        $wasDelivered = $order->status === Order::STATUS_DELIVERED;
        $order->update($data);
        $statusMessage = 'Estado del pedido actualizado.';

        if (! $wasDelivered && $order->status === Order::STATUS_DELIVERED && ! $order->review_invite_sent_at) {
            $message = $automation->dispatchForDelivered($order->fresh());

            if ($message) {
                $statusMessage .= ' La invitación para dejar una reseña quedó en cola de WhatsApp.';
            } elseif (! $order->whatsapp_consent_at) {
                $statusMessage .= ' No se programó WhatsApp porque el cliente no dio consentimiento.';
            } else {
                $statusMessage .= ' WhatsApp está deshabilitado o la automatización no está activa.';
            }
        }

        return back()->with('status', $statusMessage);
    }
}
