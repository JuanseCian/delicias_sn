<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

test('the home includes the most ordered section and links to the full catalog', function () {
    $this->seed();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('mas-pedidos', false)
        ->assertSee(route('catalog.index'), false)
        ->assertSee('Los más');

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('href="#categoria-tortas"', false)
        ->assertSee('id="categoria-tortas"', false)
        ->assertSee('categoria-tartas', false)
        ->assertSee('categoria-cupcakes', false)
        ->assertSee('categoria-cookies', false)
        ->assertSee('Todos los')
        ->assertSee('Choco & dulce');
});

test('the most ordered section ranks products by non-cancelled quantities', function () {
    $this->seed();
    $popularProduct = Product::with('sizes')->where('slug', 'mesa-mini-casamiento')->firstOrFail();
    $cancelledProduct = Product::with('sizes')->where('slug', 'box-sorpresa')->firstOrFail();

    $order = Order::create([
        'code' => 'DSN-CATALOG-1',
        'customer_name' => 'Cliente de prueba',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDay(),
        'time_slot' => '13 a 15 h',
        'payment_method' => 'whatsapp',
        'subtotal' => 10000,
        'deposit' => 5000,
        'status' => Order::STATUS_DELIVERED,
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $popularProduct->id,
        'product_size_id' => $popularProduct->sizes->firstOrFail()->id,
        'quantity' => 8,
        'unit_price' => 10000,
    ]);

    $cancelledOrder = $order->replicate(['access_token']);
    $cancelledOrder->code = 'DSN-CATALOG-2';
    $cancelledOrder->status = Order::STATUS_CANCELLED;
    $cancelledOrder->save();
    OrderItem::create([
        'order_id' => $cancelledOrder->id,
        'product_id' => $cancelledProduct->id,
        'product_size_id' => $cancelledProduct->sizes->firstOrFail()->id,
        'quantity' => 50,
        'unit_price' => 10000,
    ]);

    $content = $this->get(route('home'))->getContent();

    expect($content)->toContain('data-most-ordered-product="mesa-mini-casamiento"')
        ->and($content)->toContain('data-most-ordered-product="choco-dulce"')
        ->and($content)->not->toContain('data-most-ordered-product="box-sorpresa"')
        ->and(strpos($content, 'data-most-ordered-product="mesa-mini-casamiento"'))
        ->toBeLessThan(strpos($content, 'data-most-ordered-product="choco-dulce"'));
});

test('the legacy reviews URL redirects to the home carousel', function () {
    $this->get(route('reviews.public'))
        ->assertRedirect(route('home').'/'.'#opiniones');
});
