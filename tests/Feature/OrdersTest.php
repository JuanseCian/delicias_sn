<?php

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderCalculator;
use Carbon\CarbonImmutable;

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
        ->and($order->deposit)->toBe($size->price)
        ->and($order->payment_method)->toBe('whatsapp');

    $this->get(route('orders.confirmed', $order->code))
        ->assertOk()
        ->assertSee('wa.me', false)
        ->assertSee('Tu opinión')
        ->assertSee('¿Qué te pareció?');
});

test('a reservation accepts carts saved with the legacy size key', function () {
    $this->seed();
    $product = Product::with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $date = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    $response = $this->post(route('orders.store'), [
        'items' => [[
            'product_id' => $product->id,
            'size_id' => $size->id,
            'quantity' => 1,
        ]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => $date->format('Y-m-d'),
        'time_slot' => '12 a 15 h',
        'payment_method' => 'efectivo',
    ]);

    $response->assertRedirect();
    expect(Order::count())->toBe(1);
});

test('a reservation rejects a date before the longest lead time', function () {
    $this->seed();
    $product = Product::with('sizes')->where('is_custom', false)->firstOrFail();
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

test('a Sunday can be selected as a delivery date', function () {
    CarbonImmutable::setTestNow('2026-11-13 10:00:00');
    $this->seed();
    $product = Product::where('name', 'Cookies chunky')->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $minimumDate = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    expect($minimumDate->isSunday())->toBeTrue();

    CarbonImmutable::setTestNow();
});

test('a reservation is available at least two days from today', function () {
    CarbonImmutable::setTestNow('2026-11-17 10:00:00');
    $this->seed();
    $product = Product::where('name', 'Cookies chunky')->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $minimumDate = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    expect($minimumDate->toDateString())->toBe('2026-11-19');

    CarbonImmutable::setTestNow();
});

test('checkout preserves entered data after validation errors', function () {
    $this->seed();
    $product = Product::with('sizes')->where('is_custom', false)->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $date = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    $response = $this->from(route('checkout'))->post(route('orders.store'), [
        'items' => [['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '',
        'delivery_type' => 'retiro',
        'delivery_date' => $date->format('Y-m-d'),
        'time_slot' => '12 a 15 h',
        'payment_method' => 'transferencia',
    ]);

    $response->assertRedirect(route('checkout'));
    $this->get(route('checkout'))->assertSee('Ada Lovelace');
});

test('custom products are displayed as examples but cannot be reserved', function () {
    $this->seed();
    $product = Product::with('sizes')->where('is_custom', true)->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $response = $this->from(route('checkout'))->post(route('orders.store'), [
        'items' => [[
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => 1,
        ]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDays(8)->format('Y-m-d'),
        'time_slot' => '12 a 15 h',
        'payment_method' => 'efectivo',
    ]);

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHasErrors('items');
    expect(Order::count())->toBe(0);
});
