<?php

namespace App\Services;

use App\Models\Order;

class WhatsAppMessage
{
    public function forOrder(Order $order): string
    {
        $lines = [
            'Hola Delicias Sn! Quiero enviar el comprobante del pedido '.$order->code.'.',
            '',
            'Reserva: '.$order->code,
            'Total: '.format_price($order->subtotal),
            'Seña (50%): '.format_price($order->deposit),
        ];

        return implode("\n", $lines);
    }

    public function url(string $message): string
    {
        return 'https://wa.me/'.config('delicias.whatsapp').'?'.http_build_query(['text' => $message]);
    }
}
