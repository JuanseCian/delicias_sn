<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class OrderCalculator
{
    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, array{product: Product, size: ProductSize, quantity: int, note: ?string, unit_price: int, total: int}>
     */
    public function resolveItems(array $items): Collection
    {
        return collect($items)->map(function (array $item): array {
            $productId = $item['product_id'] ?? null;
            $productSizeId = $item['product_size_id'] ?? null;
            $product = Product::with('category')
                ->where('active', true)
                ->where('is_custom', false)
                ->find($productId);
            $size = ProductSize::query()
                ->where('id', $productSizeId)
                ->where('product_id', $productId)
                ->first();
            $color = ! empty($item['color_id'])
                ? ProductColor::query()->where('id', $item['color_id'])->where('product_id', $productId)->first()
                : null;

            if (! $product || ! $size || (! empty($item['color_id']) && ! $color)) {
                throw ValidationException::withMessages([
                    'items' => 'Uno de los productos elegidos ya no está disponible.',
                ]);
            }

            $quantity = (int) $item['quantity'];

            if ($quantity > $size->quantity) {
                throw ValidationException::withMessages([
                    'items' => 'No hay cantidad disponible para uno de los tamaños elegidos.',
                ]);
            }

            return [
                'product' => $product,
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
                'note' => $item['note'] ?? null,
                'unit_price' => $size->price,
                'total' => $size->price * $quantity,
            ];
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function minimumDateForItems(array $items): CarbonImmutable
    {
        $leadTimeHours = $this->resolveItems($items)
            ->map(fn (array $item): int => $item['product']->lead_time_hours)
            ->max() ?? 0;

        $date = CarbonImmutable::now()->addHours($leadTimeHours)->startOfDay();

        while ($date->isSunday()) {
            $date = $date->addDay();
        }

        return $date;
    }

    /**
     * @param  Collection<int, array{total: int}>  $items
     * @return array{subtotal: int, deposit: int, balance: int}
     */
    public function totals(Collection $items): array
    {
        $subtotal = (int) $items->sum('total');
        $deposit = (int) ceil($subtotal / 2);

        return [
            'subtotal' => $subtotal,
            'deposit' => $deposit,
            'balance' => $subtotal - $deposit,
        ];
    }
}
