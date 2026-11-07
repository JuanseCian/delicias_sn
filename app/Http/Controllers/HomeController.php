<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::with(['category', 'sizes', 'colors', 'ingredients', 'allergens'])
            ->where('active', true)
            ->get();

        $catalog = $products->map(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => product_image_url($product->image),
            'category' => $product->category->name,
            'category_slug' => $product->category->slug,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'storage_instructions' => $product->storage_instructions,
            'is_custom' => $product->is_custom,
            'lead_time_hours' => $product->lead_time_hours,
            'sizes' => $product->sizes->map(fn ($size): array => ['id' => $size->id, 'label' => $size->label, 'price' => $size->price, 'quantity' => $size->quantity])->values(),
            'colors' => $product->colors->map(fn ($color): array => ['id' => $color->id, 'name' => $color->name, 'hex' => $color->hex])->values(),
            'ingredients' => $product->ingredients->map(fn ($ingredient): array => ['name' => $ingredient->name, 'origin' => $ingredient->origin])->values(),
            'allergens' => $product->allergens->pluck('name')->values(),
        ])->values();

        return view('home', [
            'categories' => Category::orderBy('order')->get(),
            'products' => $products,
            'featured' => $products->firstWhere('is_featured', true),
            'ingredients' => Ingredient::orderBy('id')->get(),
            'leadTimes' => $products->mapWithKeys(fn (Product $product): array => [$product->id => $product->lead_time_hours]),
            'catalog' => $catalog,
        ]);
    }
}
