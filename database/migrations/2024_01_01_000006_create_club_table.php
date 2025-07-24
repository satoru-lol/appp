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
        Schema::create('club', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->string('times')->nullable();
            $table->datetime('date')->nullable();
            $table->string('speakers')->nullable();
            $table->string('theory')->nullable();
            $table->string('feedback')->nullable();
            $table->text('text')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('video')->nullable();
            $table->integer('product_level')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->string('practice')->nullable();
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('slug')->nullable()->unique();
            $table->text('short_description')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->decimal('price', 8, 2)->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable();
            $table->integer('views')->default(0);
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['product_level', 'is_hidden']);
            $table->index(['date', 'is_hidden']);
            $table->index('slug');
            $table->index(['is_featured', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('club');
    }
};