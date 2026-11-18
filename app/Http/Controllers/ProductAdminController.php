<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\OrderItem;
use App\Models\Product;
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

        return to_route('private.products.edit', $product)->with('success', 'Producto creado. Ya podés verlo en la página.');
    }

    public function edit(Product $product): View
    {
        $product->load(['sizes', 'ingredients', 'allergens']);

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
        $product->delete();

        return to_route('private.products.index')->with('success', 'Producto archivado. Los pedidos existentes se conservaron.');
    }

    /** @return array<string, mixed> */
    private function formData(?Product $product = null): array
    {
        return [
            'product' => $product,
            'categories' => Category::orderBy('order')->get(),
        ];
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
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
            'ingredients_text' => ['nullable', 'string', 'max:2000'],
            'allergens' => ['nullable', 'array'],
            'allergens.*' => ['integer', 'exists:allergens,id'],
        ], [
            'sizes.required' => 'Agregá al menos un tamaño.',
            'sizes.*.label.required' => 'Cada tamaño necesita un nombre.',
            'sizes.*.price.required' => 'Cada tamaño necesita un precio.',
            'image.max' => 'La imagen no puede pesar más de 5 MB.',
        ]);
    }

    /** @param array<string, mixed> $data */
    private function productAttributes(array $data, ?Product $product = null): array
    {
        $baseSlug = Str::slug($data['name']) ?: 'producto';
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::withTrashed()
            ->where('slug', $slug)
            ->when($product, fn ($query) => $query->whereKeyNot($product->id))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'short_description' => $data['description'],
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

    }

    /** @param array<string, mixed> $data */
    private function saveRelations(Product $product, array $data): void
    {
        $ingredientIds = collect(preg_split('/[\r\n,]+/', trim((string) ($data['ingredients_text'] ?? '')), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $name): int => Ingredient::firstOrCreate(
                ['name' => trim($name)],
                ['origin' => 'Carga manual'],
            )->id)
            ->unique()
            ->values()
            ->all();

        $product->ingredients()->sync($ingredientIds);
        if (array_key_exists('allergens', $data)) {
            $product->allergens()->sync($data['allergens']);
        }
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
