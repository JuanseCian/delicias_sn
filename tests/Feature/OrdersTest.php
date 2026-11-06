<?php

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderCalculator;

test('a customer can reserve a seeded order', function () {
    $this->seed();
    $product = Product::with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $date = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 2],
    ]);

    $response = $this->post(route('orders.store'), [
        'items' => [[
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => 2,
            'note' => 'Sin tarjeta',
        ]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => $date->format('Y-m-d'),
        'time_slot' => '12 a 15 h',
        'payment_method' => 'transferencia',
    ]);

    $order = Order::firstOrFail();

    $response->assertRedirect(route('orders.confirmed', $order->code));
    expect($order->code)->toStartWith('DSN-')
        ->and($order->subtotal)->toBe($size->price * 2)
        ->and($order->deposit)->toBe($size->price);
});

test('a reservation rejects a date before the longest lead time', function () {
    $this->seed();
    $product = Product::with('sizes')->where('is_custom', true)->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $date = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ])->subDay();

    $response = $this->from(route('checkout'))->post(route('orders.store'), [
        'items' => [[
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => 1,
        ]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => $date->format('Y-m-d'),
        'time_slot' => '12 a 15 h',
        'payment_method' => 'efectivo',
    ]);

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHasErrors('delivery_date');
    expect(Order::count())->toBe(0);
});
