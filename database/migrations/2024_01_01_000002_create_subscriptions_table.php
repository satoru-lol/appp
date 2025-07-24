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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('level')->default(1);
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('test')->nullable();
            $table->boolean('test_period')->nullable();
            $table->boolean('auto')->default(false);
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'is_active']);
            $table->index(['level', 'expired_at']);
            $table->index(['is_active', 'expired_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};