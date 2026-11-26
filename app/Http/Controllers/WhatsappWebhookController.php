<?php

namespace App\Http\Controllers;

use App\Models\WhatsappMessage;
use App\Models\WhatsappWebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = config('services.evolution.webhook_secret');
        if (filled($secret) && ! hash_equals((string) $secret, (string) $request->query('token'))) {
            abort(401);
        }

        $payload = $request->all();
        $eventName = (string) ($payload['event'] ?? 'unknown');
        $providerEventId = data_get($payload, 'data.key.id');
        $eventKey = filled($providerEventId) ? (string) $providerEventId : sha1((string) json_encode($payload));
        $event = WhatsappWebhookEvent::query()->firstOrCreate(
            ['event_key' => $eventName.':'.$eventKey],
            ['event_name' => $eventName, 'payload' => $payload],
        );

        if ($event->wasRecentlyCreated) {
            $providerId = data_get($payload, 'data.key.id');
            $status = strtolower((string) data_get($payload, 'data.status'));
            $fromMe = (bool) data_get($payload, 'data.key.fromMe', false);
            $incomingBody = data_get($payload, 'data.message.conversation')
                ?? data_get($payload, 'data.message.extendedTextMessage.text');

            if (filled($providerId) && ! $fromMe && filled($incomingBody)) {
                WhatsappMessage::query()->firstOrCreate(
                    ['provider_message_id' => $providerId],
                    [
                        'direction' => 'inbound',
                        'type' => 'text',
                        'recipient' => data_get($payload, 'data.key.remoteJid'),
                        'body' => $incomingBody,
                        'status' => 'received',
                        'provider_payload' => $payload,
                    ],
                );
            }

            if (filled($providerId) && filled($status)) {
                WhatsappMessage::query()->where('provider_message_id', $providerId)->update([
                    'status' => match ($status) {
                        'delivered', 'delivery_ack', 'read', 'played' => 'delivered',
                        'failed', 'error' => 'failed',
                        default => 'sent',
                    },
                ]);
            }
        }

        return response()->json(['ok' => true, 'duplicate' => ! $event->wasRecentlyCreated]);
    }
}
