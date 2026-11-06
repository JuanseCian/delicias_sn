<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('dashboard', [
            'stats' => [
                'products' => Product::where('active', true)->count(),
                'categories' => Category::count(),
                'with_images' => Product::where('active', true)->whereNotNull('image')->count(),
            ],
        ]);
    }
}
