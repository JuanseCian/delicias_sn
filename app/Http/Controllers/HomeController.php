<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $approvedReviews = Review::query()
            ->where('approved', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
        $products = Product::with(['category', 'sizes', 'ingredients', 'allergens'])
            ->where('active', true)
            ->where('is_custom', false)
            ->get();
        $mostOrdered = Product::with(['category', 'sizes'])
            ->withSum(['orderItems as ordered_quantity' => function (Builder $query): void {
                $query->whereHas('order', function (Builder $orderQuery): void {
                    $orderQuery->where('status', '!=', Order::STATUS_CANCELLED);
                });
            }], 'quantity')
            ->where('active', true)
            ->where('is_custom', false)
            ->orderByDesc('ordered_quantity')
            ->orderByDesc('is_featured')
            ->orderBy('id')
            ->take(6)
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
        $railProducts = $mostOrdered->map(fn (Product $product): array => [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'category' => $product->category->name,
            'description' => $product->short_description,
            'price' => format_price($product->starting_price),
            'image' => product_image_url($product->image),
            'tag' => $product->ordered_quantity > 0 ? 'Más pedido' : 'Favorito',
            'details' => $product->sizes->take(2)->pluck('label')->values(),
            'url' => route('catalog.products.show', ['product' => $product->slug]),
        ])->values();

        return view('home', [
            'categories' => Category::whereHas('products', function ($query): void {
                $query->where('active', true)->where('is_custom', false);
            })->orderBy('order')->get(),
            'products' => $products,
            'featured' => $products->where('is_featured', true)->take(4)->values(),
            'leadTimes' => $products->mapWithKeys(fn (Product $product): array => [$product->id => $product->lead_time_hours]),
            'catalog' => $catalog,
            'mostOrdered' => $mostOrdered,
            'railProducts' => $railProducts,
            'reviews' => $approvedReviews->take(6),
            'stats' => [
                'count' => $approvedReviews->count(),
                'average' => round((float) ($approvedReviews->avg('rating') ?? 0), 1),
            ],
        ]);
    }
}
