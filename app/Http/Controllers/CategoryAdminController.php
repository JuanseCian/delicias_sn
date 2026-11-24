<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    public function index(): View
    {
        return view('private.categories.index', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->withTrashed()])
                ->orderBy('order')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'lead_time_hours' => ['required', 'integer', 'min:1', 'max:65535'],
            'order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $baseSlug = Str::slug($data['name']) ?: 'categoria';
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        Category::create([
            'name' => $data['name'],
            'slug' => $slug,
            'lead_time_hours' => $data['lead_time_hours'],
            'order' => $data['order'] ?? ((int) Category::max('order') + 1),
        ]);

        return to_route('private.categories.index')->with('success', 'Categoría creada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Esta categoría todavía tiene productos. Movelos o eliminalos antes de quitarla.',
            ]);
        }

        $category->delete();

        return to_route('private.categories.index')->with('success', 'Categoría eliminada.');
    }
}
