<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
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
