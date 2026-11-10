<?php

namespace App\Http\Controllers;

use App\Models\CustomizerOption;
use App\Models\Product;
use Illuminate\View\View;

class PersonalizationController extends Controller
{
    public function __invoke(): View
    {
        $customProducts = Product::query()
            ->where('active', true)
            ->where('is_custom', true)
            ->orderBy('name')
            ->get();
        $customizerOptions = CustomizerOption::query()
            ->where('active', true)
            ->orderBy('kind')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (CustomizerOption $option): array => [
                'id' => $option->id,
                'kind' => $option->kind,
                'slug' => $option->slug,
                'name' => $option->name,
                'description' => $option->description,
                'metadata' => $option->metadata ?? [],
            ])
            ->values();

        return view('personalizadas', [
            'customProducts' => $customProducts,
            'customizerOptions' => $customizerOptions,
            'catalog' => [],
        ]);
    }
}
