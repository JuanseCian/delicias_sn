<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('format_price')) {
    function format_price(int|float|null $price): string
    {
        return '$ '.number_format((float) $price, 0, ',', '.');
    }
}

if (! function_exists('product_image_url')) {
    function product_image_url(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        return Str::startsWith($image, ['http://', 'https://'])
            ? $image
            : Storage::disk('public')->url($image);
    }
}

if (! function_exists('delicias_time_slots')) {
    /**
     * Franjas horarias de entrega, con valores por defecto si la configuración
     * no las trae (por ejemplo, una caché de config vieja en el servidor).
     *
     * @return array{weekday: list<string>, weekend: list<string>}
     */
    function delicias_time_slots(): array
    {
        $defaults = [
            'weekday' => ['13 a 15 h', '15 a 18 h', '18 a 20 h'],
            'weekend' => ['9 a 12 h', '12 a 15 h', '15 a 18 h', '18 a 20 h'],
        ];
        $configured = config('delicias.time_slots');

        if (! is_array($configured)) {
            return $defaults;
        }

        return collect($defaults)
            ->map(fn (array $fallback, string $key) => is_array($configured[$key] ?? null) && $configured[$key] !== []
                ? array_values(array_map('strval', $configured[$key]))
                : $fallback)
            ->all();
    }
}
