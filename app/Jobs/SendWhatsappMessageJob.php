<?php

namespace App\Jobs;

use App\Models\WhatsappMessage;
use App\Services\EvolutionApiService;
use App\Services\WhatsappAutomationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $messageId) {}

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(EvolutionApiService $evolution, WhatsappAutomationService $automation): void
    {
        $message = WhatsappMessage::query()->findOrFail($this->messageId);

        if ($message->status === 'sent') {
            return;
        }

        if ($message->order && ($scheduledAt = $automation->scheduledAt($message->order)) && now()->lessThan($scheduledAt)) {
            $this->release(max(60, now()->diffInSeconds($scheduledAt)));

            return;
        }

        $message->increment('attempts');
        $message->refresh();
        $message->update(['status' => 'sending', 'error_message' => null]);

        try {
            $payload = $evolution->sendText((string) $message->recipient, (string) $message->body);
            $message->update([
                'status' => 'sent',
                'provider_message_id' => data_get($payload, 'key.id') ?? data_get($payload, 'message.key.id'),
                'provider_payload' => $payload,
                'sent_at' => now(),
            ]);

            if ($message->automation_key === 'review_after_delivery' && $message->order) {
                $message->order->forceFill(['review_invite_sent_at' => now()])->saveQuietly();
            }
        } catch (Throwable $exception) {
            $message->update([
                'status' => $message->attempts >= $this->tries ? 'failed' : 'retrying',
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        WhatsappMessage::query()->whereKey($this->messageId)->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
