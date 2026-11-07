<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    public function create(): View
    {
        return view('private.products.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $product = DB::transaction(function () use ($request, $data): Product {
            $product = Product::create($this->productAttributes($data));
            $this->saveVariants($product, $data);
            $this->saveRelations($product, $data);
            $this->saveImage($request, $product);

            return $product;
        });

        return to_route('private.products.edit', $product)->with('success', 'Producto creado. Ya podés verlo en la landing.');
    }

    public function edit(Product $product): View
    {
        $product->load(['sizes', 'colors', 'ingredients', 'allergens']);

        return view('private.products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($request, $data, $product): void {
            $product->update($this->productAttributes($data, $product));
            $this->saveVariants($product, $data);
            $this->saveRelations($product, $data);
            $this->saveImage($request, $product);
        });

        return to_route('private.products.edit', $product)->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (OrderItem::where('product_id', $product->id)->exists()) {
            return to_route('private.products.index')->with('error', 'No se puede borrar un producto que ya tiene pedidos. Podés desactivarlo desde editar.');
        }

        $this->deleteStoredImage($product->image);
        $product->delete();

        return to_route('private.products.index')->with('success', 'Producto eliminado.');
    }

    /** @return array<string, mixed> */
    private function formData(?Product $product = null): array
    {
        return [
            'product' => $product,
            'categories' => Category::orderBy('order')->get(),
            'ingredients' => Ingredient::orderBy('name')->get(),
            'allergens' => Allergen::orderBy('name')->get(),
        ];
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'short_description' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:1200'],
            'storage_instructions' => ['required', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'is_custom' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*.id' => ['nullable', 'integer', 'exists:product_sizes,id'],
            'sizes.*.label' => ['required', 'string', 'max:80'],
            'sizes.*.price' => ['required', 'integer', 'min:1', 'max:99999999'],
            'sizes.*.quantity' => ['required', 'integer', 'min:0', 'max:999999'],
            'colors' => ['nullable', 'array', 'max:12'],
            'colors.*.id' => ['nullable', 'integer', 'exists:product_colors,id'],
            'colors.*.name' => ['required_with:colors.*.hex', 'string', 'max:50'],
            'colors.*.hex' => ['required_with:colors.*.name', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*' => ['integer', 'exists:ingredients,id'],
            'allergens' => ['nullable', 'array'],
            'allergens.*' => ['integer', 'exists:allergens,id'],
        ], [
            'sizes.required' => 'Agregá al menos un tamaño.',
            'sizes.*.label.required' => 'Cada tamaño necesita un nombre.',
            'sizes.*.price.required' => 'Cada tamaño necesita un precio.',
            'sizes.*.quantity.required' => 'Cada tamaño necesita una cantidad.',
            'colors.*.hex.regex' => 'Cada color debe tener un código hexadecimal válido.',
            'image.max' => 'La imagen no puede pesar más de 5 MB.',
        ]);
    }

    /** @param array<string, mixed> $data */
    private function productAttributes(array $data, ?Product $product = null): array
    {
        $slug = Str::slug($data['name']);
        $slugExists = Product::where('slug', $slug)
            ->when($product, fn ($query) => $query->whereKeyNot($product->id))
            ->exists();

        if ($slugExists) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'storage_instructions' => $data['storage_instructions'],
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_custom' => (bool) ($data['is_custom'] ?? false),
            'active' => array_key_exists('active', $data) ? (bool) $data['active'] : true,
        ];
    }

    /** @param array<string, mixed> $data */
    private function saveVariants(Product $product, array $data): void
    {
        $sizeIds = [];
        foreach ($data['sizes'] as $sizeData) {
            $size = ! empty($sizeData['id'])
                ? ProductSize::whereKey($sizeData['id'])->where('product_id', $product->id)->first()
                : null;
            $size ??= new ProductSize(['product_id' => $product->id]);
            $size->fill([
                'label' => $sizeData['label'],
                'price' => $sizeData['price'],
                'quantity' => $sizeData['quantity'],
            ])->save();
            $sizeIds[] = $size->id;
        }

        ProductSize::where('product_id', $product->id)
            ->whereNotIn('id', $sizeIds)
            ->get()
            ->each(function (ProductSize $size): void {
                if (! OrderItem::where('product_size_id', $size->id)->exists()) {
                    $size->delete();
                }
            });

        $colorIds = [];
        foreach ($data['colors'] ?? [] as $index => $colorData) {
            if (blank($colorData['name'] ?? null) || blank($colorData['hex'] ?? null)) {
                continue;
            }

            $color = ! empty($colorData['id'])
                ? ProductColor::whereKey($colorData['id'])->where('product_id', $product->id)->first()
                : null;
            $color ??= new ProductColor(['product_id' => $product->id]);
            $color->fill([
                'name' => $colorData['name'],
                'hex' => $colorData['hex'],
                'sort_order' => $index,
            ])->save();
            $colorIds[] = $color->id;
        }

        ProductColor::where('product_id', $product->id)
            ->whereNotIn('id', $colorIds)
            ->delete();
    }

    /** @param array<string, mixed> $data */
    private function saveRelations(Product $product, array $data): void
    {
        $product->ingredients()->sync($data['ingredients'] ?? []);
        $product->allergens()->sync($data['allergens'] ?? []);
    }

    private function saveImage(Request $request, Product $product): void
    {
        if (! $request->hasFile('image')) {
            return;
        }

        $this->deleteStoredImage($product->getOriginal('image'));
        $product->update(['image' => $request->file('image')->store('products', 'public')]);
    }

    private function deleteStoredImage(?string $image): void
    {
        if ($image && ! str_starts_with($image, 'http')) {
            Storage::disk('public')->delete($image);
        }
    }
}
