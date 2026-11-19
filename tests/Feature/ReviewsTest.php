<?php

use App\Models\Review;

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

test('a visitor can submit a review for moderation', function () {
    $response = $this->post(route('reviews.store'), [
        'name' => 'Ada Lovelace',
        'rating' => 5,
        'comment' => 'Me encantó la propuesta y la atención.',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('review_status');
    expect(Review::firstOrFail()->approved)->toBeFalse();
});

test('a visitor can submit a review without a comment', function () {
    $response = $this->post(route('reviews.store'), [
        'name' => 'Ada Lovelace',
        'rating' => 4,
    ]);

    $response->assertRedirect()
        ->assertSessionHas('review_status');
    expect(Review::firstOrFail()->comment)->toBe('');
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
