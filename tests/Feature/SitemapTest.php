<?php

use App\Models\Product;

test('the sitemap is valid xml and contains public canonical urls', function () {
    $this->seed();
    $product = Product::query()->where('active', true)->where('is_custom', false)->with('category')->firstOrFail();

    $response = $this->get(route('sitemap'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
        ->assertSee('https://deliciassn.com/', false)
        ->assertSee('https://deliciassn.com/productos/'.$product->slug, false)
        ->assertSee('https://deliciassn.com/categorias/'.$product->category->slug, false)
        ->assertDontSee('https://deliciassn.com/checkout', false)
        ->assertDontSee('gestion-delicias-sn-privada', false);

    $xml = simplexml_load_string($response->getContent());
    expect($xml)->not->toBeFalse();

    $locations = collect($xml->url)->map(fn ($url): string => (string) $url->loc);
    expect($locations->unique()->count())->toBe($locations->count());
});

test('public product and category urls are available only for public content', function () {
    $this->seed();
    $product = Product::query()->where('active', true)->where('is_custom', false)->with('category')->firstOrFail();

    $this->get(route('catalog.products.show', ['product' => $product->slug]))
        ->assertOk()
        ->assertSee($product->name);

    $this->get(route('catalog.categories.show', ['category' => $product->category->slug]))
        ->assertOk()
        ->assertSee($product->category->name);

    $product->update(['active' => false]);

    $this->get(route('catalog.products.show', ['product' => $product->slug]))
        ->assertNotFound();
});

test('robots declares the canonical sitemap and protects private paths', function () {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->toContain('Sitemap: https://deliciassn.com/sitemap.xml')
        ->toContain('Disallow: /checkout')
        ->toContain('Disallow: /gestion-delicias-sn-privada/');
});
