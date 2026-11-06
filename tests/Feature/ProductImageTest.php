<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a product image can be uploaded and removed from the private panel', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'admin@gmail.com')->firstOrFail());
    Storage::fake('public');
    $product = Product::firstOrFail();

    $response = $this->post(route('private.products.image.store', $product), [
        'image' => UploadedFile::fake()->image('torta-real.jpg'),
    ]);

    $product->refresh();
    $response->assertRedirect(route('private.products.index'));
    expect($product->image)->not->toBeNull();
    Storage::disk('public')->assertExists($product->image);

    $response = $this->delete(route('private.products.image.destroy', $product));

    $product->refresh();
    $response->assertRedirect(route('private.products.index'));
    expect($product->image)->toBeNull();
});
