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
        // Обновляем таблицу likes для полиморфных связей
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Полиморфные связи для лайков
            $table->string('object_name'); // 'meeting', 'course', 'video', etc.
            $table->unsignedBigInteger('object_id');
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['object_name', 'object_id']);
            $table->index(['user_id', 'object_name']);
            $table->unique(['user_id', 'object_name', 'object_id'], 'unique_user_like');
        });

        // Обновляем таблицу dislikes для полиморфных связей
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Полиморфные связи для дизлайков
            $table->string('object_name'); // 'meeting', 'course', 'video', etc.
            $table->unsignedBigInteger('object_id');
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['object_name', 'object_id']);
            $table->index(['user_id', 'object_name']);
            $table->unique(['user_id', 'object_name', 'object_id'], 'unique_user_dislike');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dislikes');
        Schema::dropIfExists('likes');
    }
};