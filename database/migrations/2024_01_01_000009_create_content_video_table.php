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
        Schema::create('content_video', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->integer('category_id');
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable(); // в секундах
            $table->integer('views')->default(0);
            $table->integer('access_level')->default(1);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('tags')->nullable();
            $table->json('seo_data')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['category_id', 'is_active']);
            $table->index(['access_level', 'is_active']);
            $table->index(['is_featured', 'views']);
            $table->index('slug');
            $table->index(['sort_order', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_video');
    }
};