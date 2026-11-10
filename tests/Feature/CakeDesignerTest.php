<?php

use App\Models\CakeDesignRequest;
use App\Models\CustomizerOption;

test('the cake designer lives on its own personalization page', function () {
    $this->seed();

    $response = $this->get(route('personalized'));

    $response->assertOk()
        ->assertSee('data-cake-designer', false)
        ->assertSee('Diseñá algo único');
});

test('a customer can save a valid cake design request', function () {
    $this->seed();
    $size = CustomizerOption::where('kind', 'size')->firstOrFail();
    $coverage = CustomizerOption::where('kind', 'coverage')->firstOrFail();
    $color = CustomizerOption::where('kind', 'color')->firstOrFail();
    $topping = CustomizerOption::where('kind', 'topping')->firstOrFail();

    $response = $this->postJson(route('custom-design-requests.store'), [
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'prompt' => 'Una torta elegante para un cumpleaños.',
        'configuration' => [
            'size_id' => $size->id,
            'coverage_id' => $coverage->id,
            'color_ids' => [$color->id],
            'topping_ids' => [$topping->id],
        ],
    ]);

    $response->assertCreated()->assertJsonPath('estimated_price', $size->price + $coverage->price + $color->price + $topping->price);
    expect(CakeDesignRequest::count())->toBe(1);
});

test('a cake design cannot use an inactive or unknown option', function () {
    $this->seed();
    $coverage = CustomizerOption::where('kind', 'coverage')->firstOrFail();
    $color = CustomizerOption::where('kind', 'color')->firstOrFail();

    $response = $this->postJson(route('custom-design-requests.store'), [
        'configuration' => [
            'size_id' => 999999,
            'coverage_id' => $coverage->id,
            'color_ids' => [$color->id],
            'topping_ids' => [],
        ],
    ]);

    $response->assertUnprocessable();
    expect(CakeDesignRequest::count())->toBe(0);
});

test('the cake ideas assistant reports when no provider is configured', function () {
    $response = $this->postJson(route('custom-design-ideas.store'), [
        'prompt' => 'Una torta rosa para un cumpleaños elegante.',
    ]);

    $response->assertOk()->assertJson([
        'available' => false,
        'ideas' => [],
    ]);
});
