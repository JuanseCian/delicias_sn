<?php

use App\Models\Order;
use App\Models\Review;
use App\Services\WhatsAppMessage;

function createOrderForReview(string $status = Order::STATUS_DELIVERED): Order
{
    return Order::create([
        'code' => 'DSN-TEST-'.random_int(1000, 9999),
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'address' => null,
        'delivery_date' => now()->addDay(),
        'time_slot' => '13 a 15 h',
        'payment_method' => 'whatsapp',
        'subtotal' => 10000,
        'deposit' => 5000,
        'status' => $status,
        'notes' => null,
    ]);
}

test('the public reviews page includes the reviews section', function () {
    $this->seed();

    $this->get(route('reviews.public'))
        ->assertOk()
        ->assertSee('opiniones', false)
        ->assertSee('Historias');
});

test('the public reviews page shows all approved reviews', function () {
    $this->seed();
    Review::create(['name' => 'Visible', 'rating' => 5, 'comment' => 'Una opinión visible en la página.', 'approved' => true]);
    Review::create(['name' => 'Oculta', 'rating' => 5, 'comment' => 'Esta opinión todavía no se publica.', 'approved' => false]);

    $this->get(route('reviews.public'))
        ->assertOk()
        ->assertSee('Visible')
        ->assertDontSee('Oculta');
});

test('a customer cannot review an order that has not been delivered yet', function () {
    $order = createOrderForReview(Order::STATUS_PENDING);

    $this->get(route('reviews.create', $order->access_token))
        ->assertOk()
        ->assertSee('Todavía no podés opinar');

    $response = $this->post(route('reviews.store'), [
        'token' => $order->access_token,
        'name' => 'Ada Lovelace',
        'rating' => 5,
        'comment' => 'Intentando opinar antes de la entrega.',
    ]);

    $response->assertRedirect(route('reviews.create', $order->access_token));
    expect(Review::count())->toBe(0);
});

test('review invitations repair legacy orders without an access token', function () {
    $order = createOrderForReview();
    $order->forceFill(['access_token' => null])->saveQuietly();

    $message = app(WhatsAppMessage::class)->reviewInvite($order->fresh());
    $token = $order->fresh()->access_token;

    expect($token)->not->toBeEmpty()
        ->and($message)->toContain(delicias_canonical_url(route('reviews.create', ['token' => $token], false)));
});

test('a customer can review an order once it has been delivered', function () {
    $order = createOrderForReview(Order::STATUS_DELIVERED);

    $this->get(route('reviews.create', $order->access_token))
        ->assertOk()
        ->assertSee('Contanos qué tal estuvo');

    $response = $this->post(route('reviews.store'), [
        'token' => $order->access_token,
        'name' => 'Ada Lovelace',
        'rating' => 5,
        'comment' => 'Me encantó la propuesta y la atención.',
    ]);

    $response->assertRedirect(route('reviews.create', $order->access_token))
        ->assertSessionHas('review_status');

    $review = Review::firstOrFail();
    expect($review->approved)->toBeFalse()
        ->and($review->order_id)->toBe($order->id);
});

test('a customer can submit a review without a comment', function () {
    $order = createOrderForReview();

    $response = $this->post(route('reviews.store'), [
        'token' => $order->access_token,
        'name' => 'Ada Lovelace',
        'rating' => 4,
    ]);

    $response->assertRedirect(route('reviews.create', $order->access_token))
        ->assertSessionHas('review_status');
    expect(Review::firstOrFail()->comment)->toBe('');
});

test('a customer cannot submit two reviews for the same order', function () {
    $order = createOrderForReview();

    $this->post(route('reviews.store'), [
        'token' => $order->access_token,
        'name' => 'Ada Lovelace',
        'rating' => 5,
        'comment' => 'Primera opinión, todo bien.',
    ]);

    $this->post(route('reviews.store'), [
        'token' => $order->access_token,
        'name' => 'Otra persona',
        'rating' => 1,
        'comment' => 'Intentando duplicar la opinión.',
    ]);

    expect(Review::count())->toBe(1);
});

test('guessing the human-readable order code instead of the access token is rejected', function () {
    $order = createOrderForReview();

    $this->get(route('reviews.create', $order->code))->assertNotFound();

    $response = $this->post(route('reviews.store'), [
        'token' => $order->code,
        'name' => 'Atacante',
        'rating' => 1,
        'comment' => 'Intentando usar el código legible en vez del token.',
    ]);

    $response->assertSessionHasErrors('token');
    expect(Review::count())->toBe(0);
});

test('only approved reviews are shown publicly', function () {
    $this->seed();
    Review::create(['name' => 'Publicado', 'rating' => 5, 'comment' => 'Una experiencia hermosa.', 'approved' => true]);
    Review::create(['name' => 'Pendiente', 'rating' => 1, 'comment' => 'Todavía no debe aparecer.', 'approved' => false]);

    $this->get(route('reviews.public'))
        ->assertSee('Publicado')
        ->assertDontSee('Pendiente');
});

test('all approved reviews are shown publicly', function () {
    $this->seed();

    foreach (range(1, 7) as $number) {
        Review::create([
            'name' => "Cliente {$number}",
            'rating' => 5,
            'comment' => "Opinión publicada {$number}.",
            'approved' => true,
        ]);
    }

    $this->get(route('reviews.public'))
        ->assertSee('Cliente 1')
        ->assertSee('Cliente 7');
});
