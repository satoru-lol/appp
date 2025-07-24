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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_category_id');
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->json('times')->nullable();
            $table->string('speakers')->nullable();
            $table->string('theory')->nullable();
            $table->string('practice')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('views')->default(0);
            $table->string('feedback', 500)->nullable();
            $table->boolean('is_polygon')->nullable();
            $table->boolean('is_hidden')->nullable();
            $table->integer('product_level')->nullable();
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->json('tags')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('certificate_template')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['course_category_id', 'status']);
            $table->index(['product_level', 'is_hidden']);
            $table->index(['status', 'start_date']);
            $table->index('slug');
            $table->index(['is_featured', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};