<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customizer_options', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 32);
            $table->string('slug');
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['kind', 'slug']);
            $table->index(['kind', 'active']);
        });

        Schema::create('cake_design_requests', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('prompt')->nullable();
            $table->json('configuration');
            $table->unsignedInteger('estimated_price')->nullable();
            $table->string('status')->default('pending');
            $table->string('preview_path')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cake_design_requests');
        Schema::dropIfExists('customizer_options');
    }
};
