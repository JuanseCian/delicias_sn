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
