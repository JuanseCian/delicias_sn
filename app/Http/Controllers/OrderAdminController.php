<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsAppMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

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

    public function update(Request $request, Order $order, WhatsAppMessage $whatsAppMessage): RedirectResponse
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
            try {
                $whatsAppMessage->sendReviewInvitation($order->fresh());
                $statusMessage .= ' La invitación para dejar una reseña fue enviada por WhatsApp.';
            } catch (Throwable $exception) {
                report($exception);
                $statusMessage .= ' No se pudo enviar automáticamente la invitación de reseña; revisá la configuración de WhatsApp Cloud API.';
            }
        }

        return back()->with('status', $statusMessage);
    }
}
