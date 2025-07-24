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
        Schema::create('subscription_pays', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('subscription_id')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->boolean('active')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('action')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('auto')->default(false);
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending');
            $table->json('payment_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['user_id', 'active']);
            $table->index(['product_id', 'active']);
            $table->index(['status', 'created_at']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_pays');
    }
};