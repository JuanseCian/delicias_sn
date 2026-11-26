<?php

use App\Jobs\SendWhatsappMessageJob;
use App\Models\Order;
use App\Models\User;
use App\Models\WhatsappMessage;
use App\Models\WhatsappWebhookEvent;
use App\Services\EvolutionApiService;
use App\Services\WhatsappAutomationService;
use Illuminate\Support\Facades\Http;

test('the whatsapp job sends text through Evolution and marks the invitation', function () {
    $this->seed();
    config([
        'services.evolution.base_url' => 'http://127.0.0.1:8080',
        'services.evolution.api_key' => 'test-key',
        'services.evolution.instance' => 'delicias',
    ]);
    Http::fake(['http://127.0.0.1:8080/message/sendText/delicias' => Http::response(['key' => ['id' => 'evolution-message-1']])]);

    $order = Order::create([
        'code' => 'DSN-2026-0101',
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'whatsapp_consent_at' => now(),
        'delivery_type' => 'retiro',
        'delivery_date' => now()->subDay(),
        'time_slot' => '15 a 18 h',
        'payment_method' => 'transferencia',
        'subtotal' => 30000,
        'deposit' => 15000,
        'status' => Order::STATUS_DELIVERED,
    ]);
    $message = WhatsappMessage::create([
        'order_id' => $order->id,
        'automation_key' => 'review_after_delivery',
        'direction' => 'outbound',
        'type' => 'text',
        'recipient' => $order->phone,
        'body' => 'Mensaje de prueba',
        'status' => 'queued',
    ]);

    (new SendWhatsappMessageJob($message->id))->handle(
        app(EvolutionApiService::class),
        app(WhatsappAutomationService::class),
    );

    expect($message->fresh()->status)->toBe('sent')
        ->and($message->fresh()->provider_message_id)->toBe('evolution-message-1')
        ->and($order->fresh()->review_invite_sent_at)->not->toBeNull();
    Http::assertSent(fn ($request): bool => $request->url() === 'http://127.0.0.1:8080/message/sendText/delicias'
        && $request->header('apikey')[0] === 'test-key'
        && $request['number'] === '5491112345678');
});

test('the evolution webhook is idempotent', function () {
    $payload = [
        'event' => 'MESSAGES_UPSERT',
        'data' => ['key' => ['id' => 'provider-1'], 'status' => 'DELIVERY_ACK'],
    ];

    $this->postJson(route('whatsapp.webhook'), $payload)->assertOk()->assertJson(['duplicate' => false]);
    $this->postJson(route('whatsapp.webhook'), $payload)->assertOk()->assertJson(['duplicate' => true]);

    expect(WhatsappWebhookEvent::count())->toBe(1);
});

test('an authenticated admin can open the whatsapp panel', function () {
    $this->seed();
    Http::fake(['*' => Http::response(['instance' => ['state' => 'open']])]);

    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail())
        ->get(route('private.whatsapp.index'))
        ->assertOk()
        ->assertSee('Automatización global')
        ->assertSee('Invitación a dejar una reseña');
});
