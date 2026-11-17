<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('status', 'reservado')->update(['status' => 'pendiente']);
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'pendiente')->update(['status' => 'reservado']);
    }
};
