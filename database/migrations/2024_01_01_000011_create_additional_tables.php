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
        // Таблица для форматов встреч
        Schema::create('meeting_format', function (Blueprint $table) {
            $table->id();
            $table->string('format')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Таблица для участия в объектах (встречи, курсы, клубы)
        Schema::create('participant_actions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('object_name')->nullable();
            $table->integer('object_id')->nullable();
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'object_name']);
            $table->index(['object_name', 'object_id']);
            $table->unique(['user_id', 'object_name', 'object_id'], 'unique_participation');
        });

        // Таблица для контроля просмотров
        Schema::create('object_view_control', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('object_id')->nullable();
            $table->string('object_name')->nullable();
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'object_name']);
            $table->index(['object_name', 'object_id']);
        });

        // Таблица для транзакций
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('shop')->nullable();
            $table->string('op_key')->nullable();
            $table->string('inv_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('accepted_perms')->nullable();
            $table->integer('product_id')->nullable();
            $table->decimal('sum', 10, 2)->nullable();
            $table->string('state')->nullable();
            $table->string('signature_value')->nullable();
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'status']);
            $table->index(['state', 'created_at']);
            $table->index('inv_id');
        });

        // Таблица для введений (introductions)
        Schema::create('introductions', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->index();
            $table->string('phone')->index();
            $table->string('hash');
            $table->timestamps();
        });

        // Таблица для дат клубов
        Schema::create('club_dates', function (Blueprint $table) {
            $table->id();
            $table->integer('club_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('speakers');
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['club_id', 'date']);
            $table->index('date');
        });

        // Таблица для разрешений продуктов
        Schema::create('product_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->boolean('video');
            $table->boolean('course');
            $table->boolean('club');
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index('product_id');
        });

        // Таблица для комментариев блогов
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_com')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('blog_id');
            $table->bigInteger('blog_content_id');
            $table->tinyInteger('status')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['blog_id', 'status']);
            $table->index(['user_id', 'blog_id']);
        });

        // Таблица для содержимого блогов
        Schema::create('blog_contents', function (Blueprint $table) {
            $table->id();
            $table->longText('text');
            $table->json('attach')->nullable();
            $table->timestamps();
        });

        // Таблица для категорий курсов
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('blog_contents');
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('product_permissions');
        Schema::dropIfExists('club_dates');
        Schema::dropIfExists('introductions');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('object_view_control');
        Schema::dropIfExists('participant_actions');
        Schema::dropIfExists('meeting_format');
    }
};