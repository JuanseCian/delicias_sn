<?php

use App\Jobs\SendWhatsappMessageJob;
use App\Models\Order;
use App\Models\User;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Queue;

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

test('marking a consented order as delivered queues one review invitation', function () {
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
        'whatsapp_consent_at' => now(),
    ]);
    $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

    WhatsappSetting::put('global_enabled', true);
    Queue::fake();

    $this->actingAs($admin)
        ->patch(route('private.orders.update', $order), ['status' => Order::STATUS_DELIVERED])
        ->assertSessionHas('status', fn (string $status): bool => str_contains($status, 'quedó en cola'));

    expect($order->fresh()->review_invite_sent_at)->toBeNull();
    Queue::assertPushed(SendWhatsappMessageJob::class, 1);

    $this->actingAs($admin)
        ->patch(route('private.orders.update', $order), ['status' => Order::STATUS_DELIVERED])
        ->assertSessionHas('status', 'Estado del pedido actualizado.');

    Queue::assertPushed(SendWhatsappMessageJob::class, 1);
});
