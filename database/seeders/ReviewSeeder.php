<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'name' => 'Camila M.',
                'rating' => 5,
                'comment' => 'La torta llegó hermosa y estaba riquísima. El cumple de mi hija fue un éxito.',
            ],
            [
                'name' => 'Nati R.',
                'rating' => 5,
                'comment' => 'Pedimos cupcakes para un festejo y fueron lo primero que desapareció de la mesa.',
            ],
            [
                'name' => 'Julián P.',
                'rating' => 4,
                'comment' => 'Muy buena atención y sabores caseros. La box llegó súper prolija y completa.',
            ],
            [
                'name' => 'Marina G.',
                'rating' => 5,
                'comment' => 'Me ayudaron a convertir una idea en una torta preciosa. Todo cuidado hasta el último detalle.',
            ],
            [
                'name' => 'Sofi T.',
                'rating' => 5,
                'comment' => 'Las cookies son increíbles, crocantes por fuera y tiernas por dentro. Volveremos a pedir.',
            ],
            [
                'name' => 'Carla V.',
                'rating' => 4,
                'comment' => 'Hermosa presentación, muy rico todo y excelente comunicación para coordinar el retiro.',
            ],
        ];

        foreach ($reviews as $review) {
            Review::updateOrCreate(
                ['name' => $review['name'], 'comment' => $review['comment']],
                [
                    'rating' => $review['rating'],
                    'approved' => true,
                    'order_id' => null,
                ],
            );
        }
    }
}
