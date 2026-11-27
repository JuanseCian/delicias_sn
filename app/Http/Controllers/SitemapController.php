<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $canonicalUrl = rtrim((string) config('delicias.canonical_url'), '/');
        $urls = collect([
            ['loc' => $canonicalUrl.'/', 'lastmod' => null],
            ['loc' => $canonicalUrl.route('faq', [], false), 'lastmod' => null],
            ['loc' => $canonicalUrl.route('personalized', [], false), 'lastmod' => null],
            ['loc' => $canonicalUrl.route('catalog.index', [], false), 'lastmod' => null],
        ])->merge($this->categoryUrls($canonicalUrl))
            ->merge($this->productUrls($canonicalUrl))
            ->unique('loc')
            ->values();

        return response()->view('sitemap', ['urls' => $urls], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /** @return Collection<int, array{loc: string, lastmod: ?string}> */
    private function categoryUrls(string $canonicalUrl): Collection
    {
        return Category::query()
            ->whereHas('products', fn ($query) => $query->where('active', true)->where('is_custom', false))
            ->orderBy('order')
            ->get()
            ->map(fn (Category $category): array => [
                'loc' => $canonicalUrl.route('catalog.categories.show', ['category' => $category->slug], false),
                'lastmod' => $category->updated_at?->toAtomString(),
            ]);
    }

    /** @return Collection<int, array{loc: string, lastmod: ?string}> */
    private function productUrls(string $canonicalUrl): Collection
    {
        return Product::query()
            ->where('active', true)
            ->where('is_custom', false)
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at'])
            ->map(fn (Product $product): array => [
                'loc' => $canonicalUrl.route('catalog.products.show', ['product' => $product->slug], false),
                'lastmod' => $product->updated_at?->toAtomString(),
            ]);
    }
}
