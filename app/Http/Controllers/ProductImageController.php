<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductImageController extends Controller
{
    public function index(): View
    {
        return view('private.products.index', [
            'products' => Product::with(['category', 'sizes', 'colors'])->orderBy('category_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'image.required' => 'Elegí una imagen antes de cargarla.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'Usá una imagen JPG, PNG o WEBP.',
            'image.max' => 'La imagen no puede pesar más de 5 MB.',
        ]);

        $this->deleteStoredImage($product->image);
        $product->update(['image' => $request->file('image')->store('products', 'public')]);

        return to_route('private.products.index')->with('success', 'Imagen actualizada para '.$product->name.'.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredImage($product->image);
        $product->update(['image' => null]);

        return to_route('private.products.index')->with('success', 'Imagen quitada de '.$product->name.'.');
    }

    private function deleteStoredImage(?string $image): void
    {
        if ($image && ! str_starts_with($image, 'http')) {
            Storage::disk('public')->delete($image);
        }
    }
}
