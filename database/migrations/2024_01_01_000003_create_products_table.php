<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->text('description');
            $table->integer('level')->default(0);
            $table->decimal('custom_price', 8, 2)->nullable();
            $table->datetime('custom_price_active_date')->nullable();
            $table->decimal('first_week_price', 8, 2)->nullable();
            $table->boolean('visible')->default(true);
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('slug')->nullable()->unique();
            $table->string('image')->nullable();
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['level', 'visible']);
            $table->index(['visible', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};