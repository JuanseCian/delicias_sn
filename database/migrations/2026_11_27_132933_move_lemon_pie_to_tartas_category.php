<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categoryId = DB::table('categories')->where('slug', 'tartas')->value('id');

        if ($categoryId === null) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => 'Tartas',
                'slug' => 'tartas',
                'lead_time_hours' => 72,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('products')
            ->where('slug', 'lemon-pie-de-campo')
            ->update(['category_id' => $categoryId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $categoryId = DB::table('categories')->where('slug', 'tortas')->value('id');

        if ($categoryId !== null) {
            DB::table('products')
                ->where('slug', 'lemon-pie-de-campo')
                ->update(['category_id' => $categoryId]);
        }
    }
};
