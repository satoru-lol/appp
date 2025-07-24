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
        Schema::table('users', function (Blueprint $table) {
            // Убеждаемся, что поле avatar существует и имеет правильный тип
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email_verified_at');
            } else {
                // Изменяем существующее поле, если нужно
                $table->string('avatar')->nullable()->change();
            }
            
            // Добавляем поле для оригинального имени файла (опционально)
            if (!Schema::hasColumn('users', 'avatar_original_name')) {
                $table->string('avatar_original_name')->nullable()->after('avatar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_original_name']);
            // Не удаляем avatar, так как он может использоваться в других местах
        });
    }
};