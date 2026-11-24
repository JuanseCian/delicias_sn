<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->whereNull('access_token')->orderBy('id')->each(function (object $order): void {
            DB::table('orders')->where('id', $order->id)->update([
                'access_token' => Str::random(48),
            ]);
        });
    }

    public function down(): void {}
};
