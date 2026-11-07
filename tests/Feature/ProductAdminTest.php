<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('an admin can create a product with sizes quantities and colors', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();

    $response = $this->post(route('private.products.store'), [
        'category_id' => $category->id,
        'name' => 'Torta demo tres colores',
        'short_description' => 'Una torta creada desde el panel.',
        'description' => 'Descripción completa de la torta creada desde administración.',
        'storage_instructions' => 'Conservar refrigerada.',
        'active' => 1,
        'sizes' => [
            ['label' => '18 cm', 'price' => 30000, 'quantity' => 4],
            ['label' => '22 cm', 'price' => 45000, 'quantity' => 6],
            ['label' => '26 cm', 'price' => 60000, 'quantity' => 8],
        ],
        'colors' => [
            ['name' => 'Rosa', 'hex' => '#C98B8B'],
            ['name' => 'Bordó', 'hex' => '#6B2D3C'],
            ['name' => 'Caramelo', 'hex' => '#C68B59'],
        ],
    ]);

    $product = Product::where('name', 'Torta demo tres colores')->with(['sizes', 'colors'])->firstOrFail();

    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->sizes)->toHaveCount(3)
        ->and($product->sizes->sum('quantity'))->toBe(18)
        ->and($product->colors)->toHaveCount(3);

    $this->get(route('home'))->assertOk()->assertSee('Torta demo tres colores');
});
