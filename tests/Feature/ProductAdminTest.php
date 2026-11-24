<?php

use App\Models\Allergen;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use App\Models\User;

test('the product form includes its custom field styles and the allergen/color sections', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());

    $this->get(route('private.products.create'))
        ->assertOk()
        ->assertSee('Nombre visible para tus clientes', false)
        ->assertSee('Alérgenos declarados')
        ->assertSee('Colores disponibles');
});

test('an admin can create a made-to-order product with sizes', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();

    $response = $this->post(route('private.products.store'), [
        'category_id' => $category->id,
        'name' => 'Torta demo',
        'description' => 'Descripción completa de la torta creada desde administración.',
        'storage_instructions' => 'Conservar refrigerada.',
        'active' => 1,
        'sizes' => [
            ['label' => '18 cm', 'price' => 30000],
            ['label' => '22 cm', 'price' => 45000],
            ['label' => '26 cm', 'price' => 60000],
        ],
    ]);

    $product = Product::where('name', 'Torta demo')->with('sizes')->firstOrFail();

    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->sizes)->toHaveCount(3)
        ->and($product->short_description)->toBe($product->description);

    $this->get(route('home'))->assertOk()->assertSee('Torta demo');
});

test('an admin can select up to four featured products', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();

    foreach (range(1, 4) as $index) {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => "Destacado de prueba {$index}",
            'slug' => "destacado-de-prueba-{$index}",
            'short_description' => 'Producto destacado.',
            'description' => 'Descripción del producto destacado.',
            'storage_instructions' => 'Conservar refrigerado.',
            'is_featured' => true,
            'active' => true,
        ]);
        ProductSize::create(['product_id' => $product->id, 'label' => 'Único', 'price' => 10000]);
    }

    $candidate = Product::create([
        'category_id' => $category->id,
        'name' => 'Quinto destacado',
        'slug' => 'quinto-destacado',
        'short_description' => 'Producto candidato.',
        'description' => 'Descripción del producto candidato.',
        'storage_instructions' => 'Conservar refrigerado.',
        'active' => true,
    ]);
    $size = ProductSize::create(['product_id' => $candidate->id, 'label' => 'Único', 'price' => 10000]);

    $this->put(route('private.products.update', $candidate), [
        'category_id' => $category->id,
        'name' => $candidate->name,
        'description' => $candidate->description,
        'storage_instructions' => $candidate->storage_instructions,
        'active' => 1,
        'is_featured' => 1,
        'sizes' => [['id' => $size->id, 'label' => $size->label, 'price' => $size->price]],
    ])->assertSessionHasErrors('is_featured');

    expect($candidate->fresh()->is_featured)->toBeFalse();
});

test('the home places featured products before the catalog', function () {
    $this->seed();

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Selección de la casa', 'El mostrador']);
});

test('the home hides categories without public products', function () {
    $this->seed();
    Category::create(['name' => 'Categoría vacía', 'slug' => 'categoria-vacia', 'lead_time_hours' => 24, 'order' => 99]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Categoría vacía');
});

test('the public christmas theme can be toggled', function () {
    $this->seed();
    config(['delicias.christmas_theme_enabled' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('christmas-atmosphere', false)
        ->assertSee('brand-mark', false);

    config(['delicias.christmas_theme_enabled' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('christmas-atmosphere', false)
        ->assertDontSee('brand-mark-hat', false);
});

test('an admin can write custom ingredients when creating a product', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();

    $response = $this->post(route('private.products.store'), [
        'category_id' => $category->id,
        'name' => 'Torta con ingredientes propios',
        'description' => 'Descripción de prueba.',
        'storage_instructions' => 'Conservar refrigerada.',
        'ingredients_text' => "Harina de avena\nCrema de coco, Ralladura de limón",
        'active' => 1,
        'sizes' => [
            ['label' => '18 cm', 'price' => 30000],
        ],
    ]);

    $product = Product::where('name', 'Torta con ingredientes propios')->with('ingredients')->firstOrFail();

    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->ingredients->pluck('name')->all())->toEqual([
        'Harina de avena',
        'Crema de coco',
        'Ralladura de limón',
    ]);
    expect(Ingredient::where('name', 'Harina de avena')->value('origin'))->toBe('Carga manual');

    $this->get(route('private.products.edit', $product))
        ->assertOk()
        ->assertSee('name="ingredients_text"', false)
        ->assertSee('Harina de avena', false)
        ->assertSee('Ralladura de limón', false);
});

test('an admin can create a product when its slug belongs to an archived product', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();

    $archivedProduct = Product::create([
        'category_id' => $category->id,
        'name' => 'Torta archivada',
        'slug' => 'torta',
        'short_description' => 'Producto archivado.',
        'description' => 'Producto archivado para probar slugs repetidos.',
        'image' => 'products/archived.webp',
        'storage_instructions' => 'Conservar refrigerada.',
    ]);
    $archivedProduct->delete();

    $response = $this->post(route('private.products.store'), [
        'category_id' => $category->id,
        'name' => 'Torta',
        'description' => 'Descripción de la torta nueva.',
        'storage_instructions' => 'Conservar refrigerada.',
        'active' => 1,
        'sizes' => [
            ['label' => '18 cm', 'price' => 30000],
        ],
    ]);

    $product = Product::where('name', 'Torta')->firstOrFail();

    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->slug)->toBe('torta-2');
});

test('an admin can update a product and remove an unused size', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $product = Product::where('is_custom', false)->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();

    $response = $this->put(route('private.products.update', $product), [
        'category_id' => $product->category_id,
        'name' => $product->name.' actualizado',
        'description' => 'Descripción actualizada del producto.',
        'storage_instructions' => 'Conservar refrigerada.',
        'active' => 1,
        'sizes' => [
            ['id' => $size->id, 'label' => $size->label, 'price' => $size->price],
        ],
    ]);

    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->fresh()->sizes)->toHaveCount(1);
});

test('an admin can archive a product without removing existing order items', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $product = Product::where('is_custom', false)->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $order = Order::create([
        'code' => 'DSN-2026-0088',
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDays(4),
        'time_slot' => '15 a 18 h',
        'payment_method' => 'transferencia',
        'subtotal' => $size->price,
        'deposit' => (int) ceil($size->price / 2),
        'status' => Order::STATUS_PENDING,
    ]);
    $item = $order->items()->create([
        'product_id' => $product->id,
        'product_size_id' => $size->id,
        'quantity' => 1,
        'unit_price' => $size->price,
    ]);

    $this->delete(route('private.products.destroy', $product))
        ->assertRedirect(route('private.products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
    expect(OrderItem::with('product')->findOrFail($item->id)->product)->not->toBeNull();
});

test('an admin can create a product with colors and allergens', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();
    $allergen = Allergen::where('name', 'Gluten')->firstOrFail();

    $response = $this->post(route('private.products.store'), [
        'category_id' => $category->id,
        'name' => 'Torta con colores',
        'description' => 'Descripción completa del producto con colores.',
        'storage_instructions' => 'Conservar refrigerada.',
        'active' => 1,
        'sizes' => [['label' => '18 cm', 'price' => 30000]],
        'colors' => [['name' => 'Rosa pastel', 'hex' => '#F4D9D5']],
        'allergens' => [$allergen->id],
    ]);

    $product = Product::where('name', 'Torta con colores')->with(['colors', 'allergens'])->firstOrFail();
    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->colors)->toHaveCount(1)
        ->and($product->colors->first()->name)->toBe('Rosa pastel')
        ->and($product->colors->first()->hex)->toBe('#F4D9D5')
        ->and($product->allergens->pluck('id')->all())->toBe([$allergen->id]);
});

test('an admin can unselect every allergen from a product', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $product = Product::where('is_custom', false)->with(['sizes', 'allergens'])->firstOrFail();
    expect($product->allergens)->not->toBeEmpty();
    $size = $product->sizes->firstOrFail();

    $this->put(route('private.products.update', $product), [
        'category_id' => $product->category_id,
        'name' => $product->name,
        'description' => $product->description,
        'storage_instructions' => $product->storage_instructions,
        'active' => 1,
        'sizes' => [['id' => $size->id, 'label' => $size->label, 'price' => $size->price]],
        // Sin "allergens": simula haber desmarcado todas las casillas.
    ])->assertRedirect(route('private.products.edit', $product));

    expect($product->fresh()->allergens)->toBeEmpty();
});

test('an admin can remove a color that was never ordered', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $product = Product::where('is_custom', false)->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Celeste', 'hex' => '#A8C8E0']);

    $this->put(route('private.products.update', $product), [
        'category_id' => $product->category_id,
        'name' => $product->name,
        'description' => $product->description,
        'storage_instructions' => $product->storage_instructions,
        'active' => 1,
        'sizes' => [['id' => $size->id, 'label' => $size->label, 'price' => $size->price]],
        'colors' => [],
    ])->assertRedirect(route('private.products.edit', $product));

    expect(ProductColor::find($color->id))->toBeNull();
});

test('an admin cannot remove a color that was already ordered', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $product = Product::where('is_custom', false)->with('sizes')->firstOrFail();
    $size = $product->sizes->firstOrFail();
    $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Celeste', 'hex' => '#A8C8E0']);
    $order = Order::create([
        'code' => 'DSN-2026-0099',
        'customer_name' => 'Ada Lovelace',
        'phone' => '11 1234 5678',
        'delivery_type' => 'retiro',
        'delivery_date' => now()->addDays(4),
        'time_slot' => '15 a 18 h',
        'payment_method' => 'transferencia',
        'subtotal' => $size->price,
        'deposit' => (int) ceil($size->price / 2),
        'status' => Order::STATUS_PENDING,
    ]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_size_id' => $size->id,
        'product_color_id' => $color->id,
        'quantity' => 1,
        'unit_price' => $size->price,
    ]);

    $this->put(route('private.products.update', $product), [
        'category_id' => $product->category_id,
        'name' => $product->name,
        'description' => $product->description,
        'storage_instructions' => $product->storage_instructions,
        'active' => 1,
        'sizes' => [['id' => $size->id, 'label' => $size->label, 'price' => $size->price]],
        'colors' => [],
    ])->assertRedirect(route('private.products.edit', $product));

    expect(ProductColor::find($color->id))->not->toBeNull();
});
