<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class EvolutionApiService
{
    public function fetchInstances(): array
    {
        return $this->request()->get('/instance/fetchInstances')->throw()->json();
    }

    public function connectionState(): array
    {
        return $this->request()->get('/instance/connectionState/'.$this->instance())->throw()->json();
    }

    public function connect(): array
    {
        return $this->request()->get('/instance/connect/'.$this->instance())->throw()->json();
    }

    public function logout(): array
    {
        return $this->request()->delete('/instance/logout/'.$this->instance())->throw()->json();
    }

    public function sendText(string $phone, string $text): array
    {
        return $this->request()
            ->post('/message/sendText/'.$this->instance(), [
                'number' => $this->normalizePhoneNumber($phone),
                'text' => $text,
            ])
            ->throw()
            ->json();
    }

    public function setWebhook(string $url): array
    {
        return $this->request()
            ->post('/webhook/set/'.$this->instance(), [
                'webhook' => [
                    'enabled' => true,
                    'url' => $url,
                    'webhookByEvents' => false,
                    'webhookBase64' => false,
                    'events' => ['MESSAGES_UPSERT', 'CONNECTION_UPDATE'],
                ],
            ])
            ->throw()
            ->json();
    }

    public function instance(): string
    {
        return (string) config('services.evolution.instance', 'delicias');
    }

    public function isConfigured(): bool
    {
        return filled(config('services.evolution.base_url')) && filled(config('services.evolution.api_key')) && filled($this->instance());
    }

    private function request(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Evolution API no está configurada. Revisá EVOLUTION_API_URL, EVOLUTION_API_KEY y EVOLUTION_INSTANCE.');
        }

        return Http::baseUrl(rtrim((string) config('services.evolution.base_url'), '/'))
            ->withHeaders(['apikey' => (string) config('services.evolution.api_key')])
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.evolution.timeout', 15));
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
