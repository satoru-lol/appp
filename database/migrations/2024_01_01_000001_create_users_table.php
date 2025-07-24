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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('group')->default('user');
            $table->string('phone')->nullable()->unique();
            $table->string('verification_code')->nullable()->index();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('auto')->default(false);
            $table->string('name')->nullable();
            $table->string('action')->nullable();
            $table->string('permissions')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('psy_lance')->nullable();
            $table->boolean('used_sub')->default(false);
            
            // Дополнительные поля для рефакторенной архитектуры
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('quick_access_token')->nullable()->index();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->json('preferences')->nullable();
            
            $table->timestamps();
            
            // Индексы для оптимизации
            $table->index(['email', 'phone_verified_at']);
            $table->index(['group', 'created_at']);
        });
        } else {
            // Таблица уже существует, добавляем только недостающие поля
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'avatar')) {
                    $table->string('avatar')->nullable()->after('used_sub');
                }
                if (!Schema::hasColumn('users', 'bio')) {
                    $table->text('bio')->nullable()->after('avatar');
                }
                if (!Schema::hasColumn('users', 'quick_access_token')) {
                    $table->string('quick_access_token')->nullable()->index()->after('bio');
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('quick_access_token');
                }
                if (!Schema::hasColumn('users', 'last_login_ip')) {
                    $table->string('last_login_ip')->nullable()->after('last_login_at');
                }
                if (!Schema::hasColumn('users', 'preferences')) {
                    $table->json('preferences')->nullable()->after('last_login_ip');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};