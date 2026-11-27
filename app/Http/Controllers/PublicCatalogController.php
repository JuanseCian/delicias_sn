<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'sizes'])
            ->where('active', true)
            ->where('is_custom', false)
            ->orderBy('category_id')
            ->orderBy('id')
            ->get();
        $railProducts = $products
            ->sortByDesc('is_featured')
            ->take(6)
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'category' => $product->category->name,
                'description' => $product->short_description,
                'price' => format_price($product->starting_price),
                'image' => product_image_url($product->image),
                'tag' => $product->is_featured ? 'Destacado' : 'Favorito',
                'details' => $product->sizes->take(2)->pluck('label')->values(),
                'url' => route('catalog.products.show', ['product' => $product->slug]),
            ])
            ->values();

        return view('catalog.index', compact('products', 'railProducts'));
    }

    public function category(Category $category): View
    {
        $products = $category->products()
            ->with(['category', 'sizes'])
            ->where('active', true)
            ->where('is_custom', false)
            ->orderBy('id')
            ->get();

        abort_if($products->isEmpty(), 404);

        return view('catalog.category', compact('category', 'products'));
    }

    public function product(Product $product): View
    {
        abort_unless($product->active && ! $product->is_custom, 404);
        $product->load(['category', 'sizes', 'ingredients', 'allergens', 'colors']);

        return view('catalog.product', compact('product'));
    }
}
