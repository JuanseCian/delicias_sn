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
            'Titular: '.$order->customer_name,
            'Teléfono: '.$order->phone,
            'Entrega: '.ucfirst($order->delivery_date->locale('es')->translatedFormat('l j \d\e F')).' · '.$order->time_slot,
            'Modalidad: '.($order->delivery_type === 'envio' ? 'Envío a domicilio' : 'Retiro por Jujuy 422'),
        ];

        if ($order->delivery_type === 'envio' && filled($order->address)) {
            $lines[] = 'Dirección: '.$order->address;
        }

        $lines[] = '';
        $lines[] = 'Detalle del pedido:';

        foreach ($order->items as $item) {
            $lines[] = '• '.$item->product->name.' · '.$item->size->label.($item->color ? ' · '.$item->color->name : '').' · x'.$item->quantity.' · '.format_price($item->unit_price * $item->quantity);
        }

        $lines[] = '';
        $lines[] = 'Total: '.format_price($order->subtotal);
        $lines[] = 'Seña (50%): '.format_price($order->deposit);

        if (filled($order->notes)) {
            $lines[] = 'Notas: '.$order->notes;
        }

        return implode("\n", $lines);
    }

    public function url(string $message): string
    {
        return 'https://wa.me/'.config('delicias.whatsapp').'?'.http_build_query(['text' => $message]);
    }
}
