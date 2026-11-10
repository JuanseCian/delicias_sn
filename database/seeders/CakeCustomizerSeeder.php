<?php

namespace Database\Seeders;

use App\Models\CustomizerOption;
use Illuminate\Database\Seeder;

class CakeCustomizerSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            ['kind' => 'size', 'slug' => '18-cm', 'name' => '18 cm', 'description' => 'Hasta 12 porciones', 'price' => 42000, 'metadata' => ['diameter_cm' => 18, 'height_cm' => 7, 'servings' => 12, 'tiers' => 1]],
            ['kind' => 'size', 'slug' => '20-cm', 'name' => '20 cm', 'description' => 'Hasta 16 porciones', 'price' => 52000, 'metadata' => ['diameter_cm' => 20, 'height_cm' => 8, 'servings' => 16, 'tiers' => 1]],
            ['kind' => 'size', 'slug' => '24-cm', 'name' => '24 cm', 'description' => 'Hasta 24 porciones', 'price' => 68000, 'metadata' => ['diameter_cm' => 24, 'height_cm' => 9, 'servings' => 24, 'tiers' => 1]],
            ['kind' => 'coverage', 'slug' => 'buttercream', 'name' => 'Buttercream', 'description' => 'Suave y artesanal', 'price' => 0, 'metadata' => ['material' => 'buttercream']],
            ['kind' => 'coverage', 'slug' => 'ganache', 'name' => 'Ganache', 'description' => 'Brillo intenso de chocolate', 'price' => 2500, 'metadata' => ['material' => 'ganache']],
            ['kind' => 'coverage', 'slug' => 'fondant', 'name' => 'Fondant', 'description' => 'Terminación lisa', 'price' => 5000, 'metadata' => ['material' => 'fondant']],
            ['kind' => 'coverage', 'slug' => 'crema', 'name' => 'Crema', 'description' => 'Textura liviana', 'price' => 1500, 'metadata' => ['material' => 'cream']],
            ['kind' => 'color', 'slug' => 'crema', 'name' => 'Crema', 'description' => null, 'price' => 0, 'metadata' => ['hex' => '#FBF5EE']],
            ['kind' => 'color', 'slug' => 'rosa-pastel', 'name' => 'Rosa pastel', 'description' => null, 'price' => 0, 'metadata' => ['hex' => '#F4D9D5']],
            ['kind' => 'color', 'slug' => 'bordo', 'name' => 'Bordó', 'description' => null, 'price' => 0, 'metadata' => ['hex' => '#6B2D3C']],
            ['kind' => 'color', 'slug' => 'caramelo', 'name' => 'Caramelo', 'description' => null, 'price' => 0, 'metadata' => ['hex' => '#C68B59']],
            ['kind' => 'color', 'slug' => 'blanco', 'name' => 'Blanco', 'description' => null, 'price' => 0, 'metadata' => ['hex' => '#FFFFFF']],
            ['kind' => 'topping', 'slug' => 'frutillas', 'name' => 'Frutillas', 'description' => 'Fruta fresca', 'price' => 3500, 'metadata' => ['type' => 'strawberry']],
            ['kind' => 'topping', 'slug' => 'cerezas', 'name' => 'Cerezas', 'description' => 'Un toque clásico', 'price' => 3000, 'metadata' => ['type' => 'cherry']],
            ['kind' => 'topping', 'slug' => 'chocolates', 'name' => 'Chocolates', 'description' => 'Piezas de chocolate', 'price' => 3500, 'metadata' => ['type' => 'chocolate']],
            ['kind' => 'topping', 'slug' => 'macarons', 'name' => 'Macarons', 'description' => 'Color y textura', 'price' => 5000, 'metadata' => ['type' => 'macaron']],
            ['kind' => 'topping', 'slug' => 'flores', 'name' => 'Flores comestibles', 'description' => 'Detalle delicado', 'price' => 4500, 'metadata' => ['type' => 'flower']],
            ['kind' => 'topping', 'slug' => 'sprinkles', 'name' => 'Sprinkles', 'description' => 'Un acabado divertido', 'price' => 1500, 'metadata' => ['type' => 'sprinkles']],
            ['kind' => 'topping', 'slug' => 'perlas', 'name' => 'Perlas decorativas', 'description' => 'Brillo sutil', 'price' => 1800, 'metadata' => ['type' => 'pearls']],
            ['kind' => 'topping', 'slug' => 'drip', 'name' => 'Drip de chocolate', 'description' => 'Gotas de chocolate', 'price' => 2500, 'metadata' => ['type' => 'drip']],
            ['kind' => 'topping', 'slug' => 'velas', 'name' => 'Velas', 'description' => 'Para celebrar', 'price' => 1000, 'metadata' => ['type' => 'candles']],
            ['kind' => 'topping', 'slug' => 'topper', 'name' => 'Topper con nombre', 'description' => 'Un detalle personal', 'price' => 1800, 'metadata' => ['type' => 'topper']],
            ['kind' => 'template', 'slug' => 'minimalista', 'name' => 'Minimalista', 'description' => 'Limpio y sereno', 'price' => 0, 'metadata' => ['coverage' => 'buttercream', 'color' => 'crema', 'toppings' => []]],
            ['kind' => 'template', 'slug' => 'elegante', 'name' => 'Elegante', 'description' => 'Sutil y sofisticado', 'price' => 0, 'metadata' => ['coverage' => 'ganache', 'color' => 'rosa-pastel', 'toppings' => ['perlas']]],
            ['kind' => 'template', 'slug' => 'infantil', 'name' => 'Infantil', 'description' => 'Colorido y alegre', 'price' => 0, 'metadata' => ['coverage' => 'buttercream', 'color' => 'rosa-pastel', 'toppings' => ['sprinkles', 'velas']]],
            ['kind' => 'template', 'slug' => 'cumpleanos', 'name' => 'Cumpleaños', 'description' => 'Listo para brindar', 'price' => 0, 'metadata' => ['coverage' => 'ganache', 'color' => 'caramelo', 'toppings' => ['chocolates', 'velas']]],
            ['kind' => 'template', 'slug' => 'romantico', 'name' => 'Romántico', 'description' => 'Suave y especial', 'price' => 0, 'metadata' => ['coverage' => 'crema', 'color' => 'rosa-pastel', 'toppings' => ['flores', 'perlas']]],
            ['kind' => 'template', 'slug' => 'vintage', 'name' => 'Vintage', 'description' => 'Clásico y ornamental', 'price' => 0, 'metadata' => ['coverage' => 'fondant', 'color' => 'crema', 'toppings' => ['flores']]],
            ['kind' => 'template', 'slug' => 'floral', 'name' => 'Floral', 'description' => 'Inspirado en el jardín', 'price' => 0, 'metadata' => ['coverage' => 'buttercream', 'color' => 'blanco', 'toppings' => ['flores', 'frutillas']]],
            ['kind' => 'template', 'slug' => 'tematico', 'name' => 'Temático', 'description' => 'Una idea con identidad', 'price' => 0, 'metadata' => ['coverage' => 'fondant', 'color' => 'bordo', 'toppings' => ['topper', 'chocolates']]],
        ];

        foreach ($options as $sortOrder => $option) {
            CustomizerOption::updateOrCreate(
                ['kind' => $option['kind'], 'slug' => $option['slug']],
                [...$option, 'sort_order' => $sortOrder],
            );
        }
    }
}
