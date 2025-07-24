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
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialist_category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->date('birthday')->nullable();
            $table->string('degree')->nullable();
            $table->integer('experience')->default(0)->comment('Опыт работы в годах');
            $table->string('location')->nullable();
            $table->text('about')->nullable();
            $table->json('prices')->nullable()->comment('Прайс-лист услуг');
            $table->json('time')->nullable()->comment('Расписание работы');
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->text('free_time')->nullable()->comment('Свободное время');
            $table->decimal('rating', 3, 2)->default(0.00)->comment('Рейтинг от 0.00 до 5.00');
            $table->boolean('status')->default(false)->comment('Статус активности');
            $table->integer('views')->default(0)->comment('Количество просмотров');
            $table->timestamps();

            // Индексы для оптимизации
            $table->index(['status', 'views']);
            $table->index(['status', 'rating']);
            $table->index('user_id');
            $table->index('specialist_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};