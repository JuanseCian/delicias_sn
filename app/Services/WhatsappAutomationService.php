<?php

namespace App\Services;

use App\Jobs\SendWhatsappMessageJob;
use App\Models\Order;
use App\Models\WhatsappAutomation;
use App\Models\WhatsappMessage;
use App\Models\WhatsappSetting;
use Carbon\CarbonImmutable;

class WhatsappAutomationService
{
    public const REVIEW_DELIVERED = 'review_after_delivery';

    public function dispatchForDelivered(Order $order): ?WhatsappMessage
    {
        if (! $this->isEnabled() || blank($order->whatsapp_consent_at)) {
            return null;
        }

        $automation = WhatsappAutomation::query()
            ->with('template')
            ->where('key', self::REVIEW_DELIVERED)
            ->where('is_enabled', true)
            ->first();

        if (! $automation?->template?->is_active) {
            return null;
        }

        $message = WhatsappMessage::query()->firstOrCreate(
            ['order_id' => $order->id, 'automation_key' => $automation->key],
            [
                'direction' => 'outbound',
                'type' => 'text',
                'recipient' => $order->phone,
                'body' => $this->render($automation->template->body, $order),
                'status' => 'queued',
            ],
        );

        if ($message->wasRecentlyCreated) {
            SendWhatsappMessageJob::dispatch($message->id)
                ->onConnection('whatsapp')
                ->onQueue('whatsapp')
                ->delay($this->scheduledAt($order, $automation->delay_minutes));
        }

        return $message;
    }

    public function render(string $body, Order $order): string
    {
        $url = delicias_canonical_url(route('reviews.create', ['token' => $order->ensureAccessToken()], false));

        return strtr($body, [
            '{{nombre}}' => $order->customer_name,
            '{{codigo}}' => $order->code,
            '{{enlace_resena}}' => $url,
            '{{fecha_entrega}}' => $order->delivery_date?->locale('es')->translatedFormat('d/m/Y') ?? '',
        ]);
    }

    public function scheduledAt(Order $order, int $delayMinutes = 0): ?CarbonImmutable
    {
        if (! $order->delivery_date) {
            return $delayMinutes > 0 ? now()->addMinutes($delayMinutes) : null;
        }

        preg_match('/^(\d{1,2})\s*a\s*\d{1,2}\s*h$/i', trim((string) $order->time_slot), $matches);
        $hour = isset($matches[1]) ? (int) $matches[1] : 0;
        $scheduledAt = CarbonImmutable::parse(
            $order->delivery_date->toDateString(),
            (string) config('delicias.timezone', 'America/Argentina/Buenos_Aires'),
        )->setTime($hour, 0);

        return $scheduledAt->addMinutes($delayMinutes);
    }

    public function isEnabled(): bool
    {
        $storedValue = WhatsappSetting::value('global_enabled');

        return $storedValue === null ? (bool) config('services.whatsapp.enabled', false) : $storedValue === '1';
    }
}
