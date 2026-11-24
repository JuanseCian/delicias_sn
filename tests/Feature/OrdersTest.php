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
        'time_slot' => $date->isWeekend() ? '15 a 18 h' : '13 a 15 h',
        'payment_method' => 'transferencia',
    ]);

    $order = Order::firstOrFail();

    $response->assertRedirect(route('orders.confirmed', $order->access_token));
    expect($order->code)->toStartWith('DSN-')
        ->and($order->access_token)->not->toBeEmpty()
        ->and($order->subtotal)->toBe($size->price * 2)
        ->and($order->deposit)->toBe($size->price)
        ->and($order->payment_method)->toBe('whatsapp');

    $this->get(route('orders.confirmed', $order->access_token))
        ->assertOk()
        ->assertSee('data-ticket-confirmation', false)
        ->assertSee('data-ticket-id="'.$order->code.'"', false)
        ->assertSee('wa.me', false);

    // El código legible del pedido no debe funcionar como ruta de confirmación: es adivinable.
    $this->get(route('orders.confirmed', $order->code))->assertNotFound();
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
        'time_slot' => $date->isWeekend() ? '15 a 18 h' : '13 a 15 h',
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
        'time_slot' => $date->isWeekend() ? '15 a 18 h' : '13 a 15 h',
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

test('a cake reservation is available at least four days from today', function () {
    CarbonImmutable::setTestNow('2026-11-17 10:00:00');
    $this->seed();
    $product = Product::where('slug', 'choco-dulce')->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $minimumDate = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    expect($minimumDate->toDateString())->toBe('2026-11-21');

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
        'time_slot' => $date->isWeekend() ? '15 a 18 h' : '13 a 15 h',
        'payment_method' => 'transferencia',
    ]);

    $response->assertRedirect(route('checkout'));
    $this->get(route('checkout'))->assertSee('Ada Lovelace');
});

test('custom products are displayed as examples but cannot be reserved', function () {
    $this->seed();
    $product = Product::with('sizes')->where('is_custom', true)->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $deliveryDate = now()->addDays(8);
    $response = $this->from(route('checkout'))->post(route('orders.store'), [
        'items' => [[
            'product_id' => $product->id,
            'product_size_id' => $size->id,
            'quantity' => 1,
        ]],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => $deliveryDate->format('Y-m-d'),
        'time_slot' => $deliveryDate->isWeekend() ? '15 a 18 h' : '13 a 15 h',
        'payment_method' => 'efectivo',
    ]);

    $response->assertRedirect(route('checkout'));
    $response->assertSessionHasErrors('items');
    expect(Order::count())->toBe(0);
});

test('reservations still work when the time slots config is missing', function () {
    config(['delicias.time_slots' => null]);
    $this->seed();
    $product = Product::with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $date = app(OrderCalculator::class)->minimumDateForItems([
        ['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1],
    ]);

    $this->get(route('checkout'))
        ->assertOk()
        ->assertSee('13 a 15 h');

    $this->post(route('orders.store'), [
        'items' => [['product_id' => $product->id, 'product_size_id' => $size->id, 'quantity' => 1, 'note' => '']],
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => $date->format('Y-m-d'),
        'time_slot' => $date->isWeekend() ? '15 a 18 h' : '13 a 15 h',
        'payment_method' => 'transferencia',
    ])->assertSessionHasNoErrors();

    expect(Order::count())->toBe(1);
});
