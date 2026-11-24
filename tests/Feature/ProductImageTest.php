<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('an admin can upload a product image from the product form', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    Storage::fake('public');
    $product = Product::with('sizes')->firstOrFail();

    $response = $this->put(route('private.products.update', $product), [
        'category_id' => $product->category_id,
        'name' => $product->name,
        'description' => $product->description,
        'storage_instructions' => $product->storage_instructions,
        'active' => 1,
        'sizes' => $product->sizes->map(fn ($size) => ['id' => $size->id, 'label' => $size->label, 'price' => $size->price])->all(),
        'image' => UploadedFile::fake()->image('torta-real.jpg'),
    ]);

    $product->refresh();
    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->image)->not->toBeNull();
    Storage::disk('public')->assertExists($product->image);
});

test('an admin can remove a product image from the private panel', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    Storage::fake('public');
    $product = Product::firstOrFail();
    $product->update(['image' => UploadedFile::fake()->image('torta.jpg')->store('products', 'public')]);

    $response = $this->delete(route('private.products.image.destroy', $product));

    $product->refresh();
    $response->assertRedirect(route('private.products.edit', $product));
    expect($product->image)->toBeNull();
});
