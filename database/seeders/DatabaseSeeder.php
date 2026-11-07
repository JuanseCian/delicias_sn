<?php

namespace Database\Seeders;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Delicias',
                'password' => Hash::make('admin123'),
            ],
        );
        $admin->forceFill(['email_verified_at' => now()])->save();

        $categories = collect([
            ['name' => 'Tortas', 'slug' => 'tortas', 'lead_time_hours' => 72, 'order' => 1],
            ['name' => 'Cupcakes', 'slug' => 'cupcakes', 'lead_time_hours' => 48, 'order' => 2],
            ['name' => 'Cookies', 'slug' => 'cookies', 'lead_time_hours' => 24, 'order' => 3],
            ['name' => 'Boxes / cajas sorpresa', 'slug' => 'boxes', 'lead_time_hours' => 48, 'order' => 4],
            ['name' => 'Mesas dulces para eventos', 'slug' => 'mesas-dulces', 'lead_time_hours' => 240, 'order' => 5],
        ])->mapWithKeys(fn (array $category): array => [$category['slug'] => Category::updateOrCreate(
            ['slug' => $category['slug']],
            $category,
        )]);

        $ingredients = collect([
            ['name' => 'Huevos de granja', 'origin' => 'Granja familiar de Luján'],
            ['name' => 'Manteca de tambo', 'origin' => 'Tambo regional, Arrecifes'],
            ['name' => 'Chocolate semiamargo', 'origin' => 'Cacao seleccionado de Ecuador'],
            ['name' => 'Frutas de estación', 'origin' => 'Productores del Mercado Central'],
            ['name' => 'Harina 0000', 'origin' => 'Molino artesanal bonaerense'],
            ['name' => 'Dulce de leche', 'origin' => 'Lácteos de campo, Tandil'],
            ['name' => 'Frutos secos', 'origin' => 'Almendras y nueces de Mendoza'],
            ['name' => 'Vainilla natural', 'origin' => 'Vainilla de Madagascar'],
        ])->mapWithKeys(fn (array $ingredient): array => [$ingredient['name'] => Ingredient::updateOrCreate(
            ['name' => $ingredient['name']],
            $ingredient,
        )]);

        $allergens = collect(['Gluten', 'Lácteos', 'Huevo', 'Frutos secos', 'Soja'])
            ->mapWithKeys(fn (string $name): array => [$name => Allergen::updateOrCreate(['name' => $name])]);

        $products = [
            'tortas' => [
                ['name' => 'Choco & dulce', 'slug' => 'choco-dulce', 'short_description' => 'Bizcochuelo húmedo, dulce de leche y ganache brillante.', 'description' => 'Capas de chocolate intenso, dulce de leche artesanal y una ganache sedosa que se corta con la cuchara. La favorita para celebrar sin vueltas.', 'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Conservar refrigerada y retirar 20 minutos antes de servir.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Chocolate semiamargo', 'Harina 0000', 'Dulce de leche'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => '18 cm / 12 porciones', 'price' => 32000], ['label' => '22 cm / 20 porciones', 'price' => 47000], ['label' => '26 cm / 30 porciones', 'price' => 64000]]],
                ['name' => 'Lemon pie de campo', 'slug' => 'lemon-pie-de-campo', 'short_description' => 'Base crocante, crema de limón y merengue dorado.', 'description' => 'El equilibrio perfecto entre ácido y dulce: crema de limón recién hecha sobre una base mantecosa y merengue italiano apenas tostado.', 'image' => 'https://images.unsplash.com/photo-1519915028121-7d3463d20b13?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Guardar en heladera, en su caja, hasta 48 horas.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => '20 cm / 10 porciones', 'price' => 28000], ['label' => '24 cm / 16 porciones', 'price' => 39000]]],
                ['name' => 'La que imaginás', 'slug' => 'torta-personalizada', 'short_description' => 'Diseñamos una torta única para tu historia.', 'description' => 'Colores, sabores, flores y detalles pensados para vos. Contanos tu idea y la convertimos en una torta que se roba todas las miradas.', 'image' => 'https://images.unsplash.com/photo-1535141192574-5d4897c12636?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Mantener refrigerada y transportar sobre una superficie plana.', 'is_custom' => true, 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Vainilla natural', 'Dulce de leche'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => '18 cm / 12 porciones', 'price' => 42000], ['label' => '22 cm / 20 porciones', 'price' => 59000], ['label' => 'A medida', 'price' => 78000]]],
            ],
            'cupcakes' => [
                ['name' => 'Cupcakes de la semana', 'slug' => 'cupcakes-de-la-semana', 'short_description' => 'Masa de vainilla, frosting de frutos rojos y flores de azúcar.', 'description' => 'Un bocado suave y perfumado, coronado con crema de frutos rojos. Vienen decorados a mano, uno por uno, para que cualquier día se sienta especial.', 'image' => 'https://images.unsplash.com/photo-1587668178277-295251f900ce?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Refrigerar y consumir idealmente dentro de las 48 horas.', 'is_featured' => true, 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Harina 0000', 'Vainilla natural'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => 'Caja x6', 'price' => 18000], ['label' => 'Caja x12', 'price' => 33000], ['label' => 'Caja x24', 'price' => 61000]]],
                ['name' => 'Red velvet', 'slug' => 'red-velvet', 'short_description' => 'Miga aterciopelada y frosting de queso crema.', 'description' => 'La clásica red velvet en versión mini: miga húmeda, cacao sutil y un frosting de queso crema que invita a repetir.', 'image' => 'https://images.unsplash.com/photo-1614707267537-2b1a87c1a0f6?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Conservar en heladera y sacar 15 minutos antes de servir.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Chocolate semiamargo', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Caja x6', 'price' => 19000], ['label' => 'Caja x12', 'price' => 35000]]],
                ['name' => 'Lemon pop', 'slug' => 'lemon-pop', 'short_description' => 'Cupcake de limón, merengue y ralladura fresca.', 'description' => 'Livianos, cítricos y alegres. Un corazón de lemon curd y una nube de merengue hacen de cada cupcake una pequeña celebración.', 'image' => 'https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Mantener refrigerados en recipiente cerrado.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => 'Caja x6', 'price' => 17500], ['label' => 'Caja x12', 'price' => 32000]]],
            ],
            'cookies' => [
                ['name' => 'Cookies chunky', 'slug' => 'cookies-chunky', 'short_description' => 'Grandotas, crocantes en los bordes y tiernas al centro.', 'description' => 'Cookies estilo bakery con mucho chocolate semiamargo y una pizca de sal. Recién horneadas, con ese perfume que llena toda la casa.', 'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Guardar a temperatura ambiente en frasco hermético hasta 5 días.', 'ingredients' => ['Manteca de tambo', 'Chocolate semiamargo', 'Harina 0000', 'Huevos de granja'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Caja x6', 'price' => 12000], ['label' => 'Caja x12', 'price' => 22000], ['label' => 'Caja x24', 'price' => 41000]]],
                ['name' => 'Almendras & miel', 'slug' => 'almendras-miel', 'short_description' => 'Mantecosas, con almendras tostadas y miel de campo.', 'description' => 'Una cookie delicada y aromática, con crocante de almendras y un dulzor suave de miel pura.', 'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Guardar en frasco hermético, lejos del calor y la humedad.', 'ingredients' => ['Manteca de tambo', 'Frutos secos', 'Huevos de granja', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Frutos secos'], 'sizes' => [['label' => 'Caja x6', 'price' => 13500], ['label' => 'Caja x12', 'price' => 25000]]],
                ['name' => 'Doble chocolate', 'slug' => 'doble-chocolate', 'short_description' => 'Cacao intenso, chips y corazón bien chocolatoso.', 'description' => 'Para quienes nunca tienen suficiente chocolate: masa de cacao, chips fundidos y centro suave.', 'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Conservar en recipiente hermético a temperatura ambiente.', 'ingredients' => ['Chocolate semiamargo', 'Manteca de tambo', 'Harina 0000', 'Huevos de granja'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Caja x6', 'price' => 13000], ['label' => 'Caja x12', 'price' => 24000]]],
            ],
            'boxes' => [
                ['name' => 'Box merienda', 'slug' => 'box-merienda', 'short_description' => 'Una pausa rica con cookies, budín y mini torta.', 'description' => 'La caja que resuelve una merienda especial: algo crocante, algo húmedo y algo cremoso, todo envuelto para regalar.', 'image' => 'https://images.unsplash.com/photo-1542826438-6b8c1f7b7a2d?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Conservar en un lugar fresco; refrigerar los productos con crema.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => 'Box individual', 'price' => 22000], ['label' => 'Box para dos', 'price' => 39000], ['label' => 'Box grande', 'price' => 56000]]],
                ['name' => 'Box brunch', 'slug' => 'box-brunch', 'short_description' => 'Dulce, fresco y listo para compartir sin apuro.', 'description' => 'Una selección abundante para una mañana de festejo: mini budines, cookies, scones y fruta de estación.', 'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Refrigerar hasta el momento de servir y consumir en 24 horas.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Harina 0000', 'Dulce de leche'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo'], 'sizes' => [['label' => 'Para 4 personas', 'price' => 48000], ['label' => 'Para 8 personas', 'price' => 82000]]],
                ['name' => 'Box sorpresa', 'slug' => 'box-sorpresa', 'short_description' => 'Una caja armada con lo mejor de la semana.', 'description' => 'No sabés qué elegir, pero sabés que querés algo rico. Nosotras armamos una combinación sorpresa con nuestras especialidades.', 'image' => 'https://images.unsplash.com/photo-1606890737304-57a1ca8a5b62?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Seguir las indicaciones de cada producto incluido en la caja.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Chocolate semiamargo', 'Frutas de estación'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Box mini', 'price' => 26000], ['label' => 'Box completa', 'price' => 51000]]],
            ],
            'mesas-dulces' => [
                ['name' => 'Mesa celebración', 'slug' => 'mesa-celebracion', 'short_description' => 'Una mesa armada para que tu festejo se vea y se sienta increíble.', 'description' => 'Mini tortas, cookies decoradas, cupcakes y bocados de estación dispuestos con una estética cuidada para recibir a tus invitados.', 'image' => 'https://images.unsplash.com/photo-1557308536-ee471ef2c390?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Mantener cada pieza refrigerada hasta el momento del evento.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutas de estación', 'Chocolate semiamargo'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Hasta 20 personas', 'price' => 145000], ['label' => 'Hasta 40 personas', 'price' => 245000]]],
                ['name' => 'Mesa mini casamiento', 'slug' => 'mesa-mini-casamiento', 'short_description' => 'Bocados delicados para brindar por una historia de amor.', 'description' => 'Una mesa romántica y abundante con paleta a elección, detalles florales y bocados dulces pensados para cada invitado.', 'image' => 'https://images.unsplash.com/photo-1525253210343-1a0c7f6b8f3c?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'La conservación y montaje se coordinan según el lugar del evento.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Frutos secos', 'Vainilla natural'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Frutos secos'], 'sizes' => [['label' => 'Hasta 30 personas', 'price' => 210000], ['label' => 'Hasta 60 personas', 'price' => 360000]]],
                ['name' => 'Mesa infantil', 'slug' => 'mesa-infantil', 'short_description' => 'Color, alegría y bocados que desaparecen primero.', 'description' => 'Una propuesta divertida y colorida para cumpleaños: cookies decoradas, mini cupcakes y golosinas hechas en nuestro obrador.', 'image' => 'https://images.unsplash.com/photo-1519869325930-281384150729?auto=format&fit=crop&w=1000&q=85', 'storage_instructions' => 'Mantener refrigerada y montar cerca del horario del evento.', 'ingredients' => ['Huevos de granja', 'Manteca de tambo', 'Chocolate semiamargo', 'Harina 0000'], 'allergens' => ['Gluten', 'Lácteos', 'Huevo', 'Soja'], 'sizes' => [['label' => 'Hasta 15 personas', 'price' => 110000], ['label' => 'Hasta 30 personas', 'price' => 190000]]],
            ],
        ];

        foreach ($products as $categorySlug => $categoryProducts) {
            foreach ($categoryProducts as $data) {
                $product = Product::updateOrCreate(
                    ['slug' => $data['slug']],
                    [
                        'category_id' => $categories[$categorySlug]->id,
                        'name' => $data['name'],
                        'short_description' => $data['short_description'],
                        'description' => $data['description'],
                        'image' => null,
                        'storage_instructions' => $data['storage_instructions'],
                        'is_featured' => $data['is_featured'] ?? false,
                        'is_custom' => $data['is_custom'] ?? false,
                    ],
                );

                foreach ($data['sizes'] as $size) {
                    $product->sizes()->updateOrCreate(
                        ['label' => $size['label']],
                        ['price' => $size['price'], 'quantity' => $size['quantity'] ?? 10],
                    );
                }

                $product->colors()->delete();
                if ($data['slug'] === 'torta-personalizada') {
                    $product->colors()->createMany([
                        ['name' => 'Rosa empolvado', 'hex' => '#C98B8B', 'sort_order' => 1],
                        ['name' => 'Bordó', 'hex' => '#6B2D3C', 'sort_order' => 2],
                        ['name' => 'Caramelo', 'hex' => '#C68B59', 'sort_order' => 3],
                    ]);
                }

                $product->ingredients()->sync(collect($data['ingredients'])->map(fn (string $name): int => $ingredients[$name]->id)->all());
                $product->allergens()->sync(collect($data['allergens'])->map(fn (string $name): int => $allergens[$name]->id)->all());
            }
        }
    }
}
