<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('lead_time_hours');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->text('description');
            $table->string('image');
            $table->text('storage_instructions');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('price');
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('origin');
            $table->timestamps();
        });

        Schema::create('allergens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('product_ingredient', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'ingredient_id']);
        });

        Schema::create('product_allergen', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('allergen_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'allergen_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('delivery_type');
            $table->string('address')->nullable();
            $table->date('delivery_date');
            $table->string('time_slot');
            $table->string('payment_method');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('deposit');
            $table->string('status')->default('reservado');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_size_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_allergen');
        Schema::dropIfExists('product_ingredient');
        Schema::dropIfExists('allergens');
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
