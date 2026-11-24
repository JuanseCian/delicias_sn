<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('an admin can review and update order status', function () {
    $this->seed();
    $order = Order::create([
        'code' => 'DSN-2026-0099',
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDays(4),
        'time_slot' => '15 a 18 h',
        'payment_method' => 'transferencia',
        'subtotal' => 30000,
        'deposit' => 15000,
        'status' => Order::STATUS_PENDING,
    ]);
    $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('private.orders.index'))
        ->assertOk()
        ->assertSee($order->code)
        ->assertSee('Pendiente');

    $this->actingAs($admin)
        ->patch(route('private.orders.update', $order), ['status' => Order::STATUS_DELIVERED])
        ->assertRedirect();

    expect($order->fresh()->status)->toBe(Order::STATUS_DELIVERED);
});

test('marking an order as delivered sends one review invitation through WhatsApp Cloud API', function () {
    $this->seed();
    $order = Order::create([
        'code' => 'DSN-2026-0100',
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDays(4),
        'time_slot' => '15 a 18 h',
        'payment_method' => 'transferencia',
        'subtotal' => 30000,
        'deposit' => 15000,
        'status' => Order::STATUS_PENDING,
    ]);
    $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

    config([
        'services.whatsapp_cloud.phone_number_id' => '123456789',
        'services.whatsapp_cloud.access_token' => 'test-token',
    ]);
    Http::fake(['https://graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.test']]])]);

    $this->actingAs($admin)
        ->patch(route('private.orders.update', $order), ['status' => Order::STATUS_DELIVERED])
        ->assertSessionHas('status', fn (string $status): bool => str_contains($status, 'fue enviada'));

    expect($order->fresh()->review_invite_sent_at)->not->toBeNull();
    Http::assertSent(fn ($request): bool => $request->url() === 'https://graph.facebook.com/v22.0/123456789/messages'
        && $request['to'] === '5491112345678'
        && $request['template']['name'] === 'delicias_resena_entregada'
        && $request['template']['components'][0]['parameters'][2]['text'] === delicias_canonical_url(route('reviews.create', ['token' => $order->fresh()->access_token], false)));

    $this->actingAs($admin)
        ->patch(route('private.orders.update', $order), ['status' => Order::STATUS_DELIVERED])
        ->assertSessionHas('status', 'Estado del pedido actualizado.');

    Http::assertSentCount(1);
});
