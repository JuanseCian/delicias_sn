<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('an admin can create a category', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());

    $response = $this->post(route('private.categories.store'), [
        'name' => 'Postres helados',
        'lead_time_hours' => 24,
    ]);

    $category = Category::where('name', 'Postres helados')->firstOrFail();
    $response->assertRedirect(route('private.categories.index'));
    expect($category->slug)->toBe('postres-helados')
        ->and($category->lead_time_hours)->toBe(24);
});

test('creating a category without a name or lead time fails validation', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());

    $this->post(route('private.categories.store'), [])
        ->assertSessionHasErrors(['name', 'lead_time_hours']);

    expect(Category::count())->toBe(Category::count());
});

test('an admin can remove a category that has no products', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::create(['name' => 'Vacía', 'slug' => 'vacia', 'lead_time_hours' => 24, 'order' => 99]);

    $this->delete(route('private.categories.destroy', $category))
        ->assertRedirect(route('private.categories.index'));

    expect(Category::find($category->id))->toBeNull();
});

test('an admin cannot remove a category that still has products', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::where('slug', 'tortas')->firstOrFail();
    expect(Product::where('category_id', $category->id)->exists())->toBeTrue();

    $this->delete(route('private.categories.destroy', $category))
        ->assertSessionHasErrors('category');

    expect(Category::find($category->id))->not->toBeNull();
});

test('an admin cannot remove a category whose only products were archived', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    $category = Category::create(['name' => 'Con producto archivado', 'slug' => 'con-producto-archivado', 'lead_time_hours' => 24, 'order' => 98]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Producto archivado',
        'slug' => 'producto-archivado-cat',
        'short_description' => 'x',
        'description' => 'x',
        'storage_instructions' => 'x',
    ]);
    $product->delete();

    $this->delete(route('private.categories.destroy', $category))
        ->assertSessionHasErrors('category');

    expect(Category::find($category->id))->not->toBeNull();
});
