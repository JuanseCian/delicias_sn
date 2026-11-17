<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::with(['category', 'sizes', 'ingredients', 'allergens'])
            ->where('active', true)
            ->where('is_custom', false)
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
            'sizes' => $product->sizes->map(fn ($size): array => ['id' => $size->id, 'label' => $size->label, 'price' => $size->price])->values(),
            'ingredients' => $product->ingredients->map(fn ($ingredient): array => ['name' => $ingredient->name, 'origin' => $ingredient->origin])->values(),
            'allergens' => $product->allergens->pluck('name')->values(),
        ])->values();
        $approvedReviews = Review::query()->where('approved', true);
        $reviews = (clone $approvedReviews)->latest()->get();
        $reviewStats = [
            'count' => (clone $approvedReviews)->count(),
            'average' => round((float) ((clone $approvedReviews)->avg('rating') ?? 0), 1),
        ];

        return view('home', [
            'categories' => Category::orderBy('order')->get(),
            'products' => $products,
            'featured' => $products->firstWhere('is_featured', true),
            'leadTimes' => $products->mapWithKeys(fn (Product $product): array => [$product->id => $product->lead_time_hours]),
            'catalog' => $catalog,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
        ]);
    }
}
