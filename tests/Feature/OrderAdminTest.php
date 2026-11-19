<?php

use App\Models\Order;
use App\Models\User;

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
