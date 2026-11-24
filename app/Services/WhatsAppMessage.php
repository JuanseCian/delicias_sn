<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

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

    public function reviewInvite(Order $order): string
    {
        $url = delicias_canonical_url(route('reviews.create', ['token' => $order->ensureAccessToken()], false));

        return "¡Hola {$order->customer_name}! Esperamos que hayas disfrutado tu pedido {$order->code}. ".
            "Nos encantaría conocer tu opinión, podés dejarla acá: {$url}";
    }

    public function sendReviewInvitation(Order $order): void
    {
        if ($order->review_invite_sent_at) {
            return;
        }

        $settings = config('services.whatsapp_cloud');
        $requiredSettings = ['api_url', 'api_version', 'phone_number_id', 'access_token', 'review_template', 'template_language'];

        if (! is_array($settings) || collect($requiredSettings)->contains(fn (string $setting): bool => blank($settings[$setting] ?? null))) {
            throw new RuntimeException('WhatsApp Cloud API no está configurada para enviar invitaciones de reseña.');
        }

        $reviewUrl = delicias_canonical_url(route('reviews.create', ['token' => $order->ensureAccessToken()], false));
        $endpoint = rtrim((string) $settings['api_url'], '/').'/'.trim((string) $settings['api_version'], '/').'/'.rawurlencode((string) $settings['phone_number_id']).'/messages';
        $response = Http::withToken((string) $settings['access_token'])
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'to' => $this->normalizePhoneNumber((string) $order->phone),
                'type' => 'template',
                'template' => [
                    'name' => $settings['review_template'],
                    'language' => ['code' => $settings['template_language']],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->customer_name],
                            ['type' => 'text', 'text' => $order->code],
                            ['type' => 'text', 'text' => $reviewUrl],
                        ],
                    ]],
                ],
            ]);

        if ($response->failed()) {
            $message = (string) $response->json('error.message', 'La API de WhatsApp rechazó el mensaje.');

            throw new RuntimeException('No se pudo enviar la invitación de reseña por WhatsApp: '.$message);
        }

        $order->forceFill(['review_invite_sent_at' => now()])->saveQuietly();
    }

    public function url(string $message, ?string $phone = null): string
    {
        $phone = $phone !== null ? preg_replace('/[^0-9]/', '', $phone) : config('delicias.whatsapp');

        return 'https://wa.me/'.$phone.'?'.http_build_query(['text' => $message]);
    }

    private function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '54')) {
            return $digits;
        }

        return ltrim((string) config('delicias.whatsapp_country_code', '549'), '0').ltrim($digits, '0');
    }
}
