<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')
            ->where('slug', 'tortas')
            ->update(['lead_time_hours' => 96]);

        DB::table('categories')
            ->where('slug', '!=', 'tortas')
            ->update(['lead_time_hours' => 48]);
    }

    public function down(): void
    {
        DB::table('categories')
            ->where('slug', 'tortas')
            ->update(['lead_time_hours' => 72]);

        DB::table('categories')
            ->whereIn('slug', ['cupcakes', 'boxes'])
            ->update(['lead_time_hours' => 48]);

        DB::table('categories')
            ->where('slug', 'cookies')
            ->update(['lead_time_hours' => 24]);

        DB::table('categories')
            ->where('slug', 'mesas-dulces')
            ->update(['lead_time_hours' => 240]);
    }
};
