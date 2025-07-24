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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('blog_category_id')->nullable();
            $table->bigInteger('blog_content_id')->nullable();
            $table->string('name');
            $table->string('time_read')->nullable();
            $table->string('image')->nullable();
            $table->integer('views')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('reg')->nullable();
            $table->string('video')->nullable();
            $table->string('feedback')->nullable();
            $table->string('amount')->nullable();
            $table->integer('product_level')->nullable();
            $table->datetime('date')->nullable();
            $table->boolean('is_meeting')->nullable();
            $table->string('fio')->nullable();
            $table->integer('format_id')->nullable();
            $table->string('explanation')->nullable();
            $table->integer('quantity')->nullable();
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('slug')->nullable()->unique();
            $table->text('short_description')->nullable();
            $table->json('tags')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('dislikes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('participants_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('seo_data')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['is_meeting', 'status', 'date']);
            $table->index(['product_level', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['format_id', 'date']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};