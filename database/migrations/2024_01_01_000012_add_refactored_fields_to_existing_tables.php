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
        // Добавляем поля к таблице users, если они не существуют
        if (Schema::hasTable('users')) {
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

        // Добавляем поля к таблице subscriptions, если они не существуют
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('auto');
                }
                if (!Schema::hasColumn('subscriptions', 'cancel_reason')) {
                    $table->string('cancel_reason')->nullable()->after('cancelled_at');
                }
                if (!Schema::hasColumn('subscriptions', 'metadata')) {
                    $table->json('metadata')->nullable()->after('cancel_reason');
                }
            });
        }

        // Добавляем поля к таблице products, если они не существуют
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('visible');
                }
                if (!Schema::hasColumn('products', 'image')) {
                    $table->string('image')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('products', 'features')) {
                    $table->json('features')->nullable()->after('image');
                }
                if (!Schema::hasColumn('products', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('features');
                }
                if (!Schema::hasColumn('products', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('sort_order');
                }
            });
        }

        // Добавляем поля к таблице subscription_pays, если они не существуют
        if (Schema::hasTable('subscription_pays')) {
            Schema::table('subscription_pays', function (Blueprint $table) {
                if (!Schema::hasColumn('subscription_pays', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('auto');
                }
                if (!Schema::hasColumn('subscription_pays', 'transaction_id')) {
                    $table->string('transaction_id')->nullable()->after('payment_method');
                }
                if (!Schema::hasColumn('subscription_pays', 'status')) {
                    $table->string('status')->default('pending')->after('transaction_id');
                }
                if (!Schema::hasColumn('subscription_pays', 'payment_data')) {
                    $table->json('payment_data')->nullable()->after('status');
                }
                if (!Schema::hasColumn('subscription_pays', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_data');
                }
            });
        }

        // Добавляем поля к таблице blogs (встречи), если они не существуют
        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                if (!Schema::hasColumn('blogs', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('quantity');
                }
                if (!Schema::hasColumn('blogs', 'short_description')) {
                    $table->text('short_description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('blogs', 'tags')) {
                    $table->json('tags')->nullable()->after('short_description');
                }
                if (!Schema::hasColumn('blogs', 'likes_count')) {
                    $table->integer('likes_count')->default(0)->after('tags');
                }
                if (!Schema::hasColumn('blogs', 'dislikes_count')) {
                    $table->integer('dislikes_count')->default(0)->after('likes_count');
                }
                if (!Schema::hasColumn('blogs', 'comments_count')) {
                    $table->integer('comments_count')->default(0)->after('dislikes_count');
                }
                if (!Schema::hasColumn('blogs', 'participants_count')) {
                    $table->integer('participants_count')->default(0)->after('comments_count');
                }
                if (!Schema::hasColumn('blogs', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('participants_count');
                }
                if (!Schema::hasColumn('blogs', 'seo_data')) {
                    $table->json('seo_data')->nullable()->after('is_featured');
                }
            });
        }

        // Добавляем поля к таблице club, если они не существуют
        if (Schema::hasTable('club')) {
            Schema::table('club', function (Blueprint $table) {
                if (!Schema::hasColumn('club', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('practice');
                }
                if (!Schema::hasColumn('club', 'short_description')) {
                    $table->text('short_description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('club', 'max_participants')) {
                    $table->integer('max_participants')->nullable()->after('short_description');
                }
                if (!Schema::hasColumn('club', 'current_participants')) {
                    $table->integer('current_participants')->default(0)->after('max_participants');
                }
                if (!Schema::hasColumn('club', 'price')) {
                    $table->decimal('price', 8, 2)->nullable()->after('current_participants');
                }
                if (!Schema::hasColumn('club', 'tags')) {
                    $table->json('tags')->nullable()->after('price');
                }
                if (!Schema::hasColumn('club', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('tags');
                }
                if (!Schema::hasColumn('club', 'seo_data')) {
                    $table->json('seo_data')->nullable()->after('is_featured');
                }
                if (!Schema::hasColumn('club', 'views')) {
                    $table->integer('views')->default(0)->after('seo_data');
                }
            });
        }

        // Добавляем поля к таблице courses, если они не существуют
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('product_level');
                }
                if (!Schema::hasColumn('courses', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('courses', 'short_description')) {
                    $table->text('short_description')->nullable()->after('description');
                }
                if (!Schema::hasColumn('courses', 'price')) {
                    $table->decimal('price', 8, 2)->nullable()->after('short_description');
                }
                if (!Schema::hasColumn('courses', 'duration_hours')) {
                    $table->integer('duration_hours')->nullable()->after('price');
                }
                if (!Schema::hasColumn('courses', 'max_participants')) {
                    $table->integer('max_participants')->nullable()->after('duration_hours');
                }
                if (!Schema::hasColumn('courses', 'current_participants')) {
                    $table->integer('current_participants')->default(0)->after('max_participants');
                }
                if (!Schema::hasColumn('courses', 'tags')) {
                    $table->json('tags')->nullable()->after('current_participants');
                }
                if (!Schema::hasColumn('courses', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('tags');
                }
                if (!Schema::hasColumn('courses', 'seo_data')) {
                    $table->json('seo_data')->nullable()->after('is_featured');
                }
                if (!Schema::hasColumn('courses', 'start_date')) {
                    $table->datetime('start_date')->nullable()->after('seo_data');
                }
                if (!Schema::hasColumn('courses', 'end_date')) {
                    $table->datetime('end_date')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('courses', 'certificate_template')) {
                    $table->string('certificate_template')->nullable()->after('end_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем добавленные поля при откате миграции
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn([
                    'slug', 'description', 'short_description', 'price', 'duration_hours',
                    'max_participants', 'current_participants', 'tags', 'is_featured',
                    'seo_data', 'start_date', 'end_date', 'certificate_template'
                ]);
            });
        }

        if (Schema::hasTable('club')) {
            Schema::table('club', function (Blueprint $table) {
                $table->dropColumn([
                    'slug', 'short_description', 'max_participants', 'current_participants',
                    'price', 'tags', 'is_featured', 'seo_data', 'views'
                ]);
            });
        }

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn([
                    'slug', 'short_description', 'tags', 'likes_count', 'dislikes_count',
                    'comments_count', 'participants_count', 'is_featured', 'seo_data'
                ]);
            });
        }

        if (Schema::hasTable('subscription_pays')) {
            Schema::table('subscription_pays', function (Blueprint $table) {
                $table->dropColumn([
                    'payment_method', 'transaction_id', 'status', 'payment_data', 'paid_at'
                ]);
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['slug', 'image', 'features', 'sort_order', 'is_featured']);
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn(['cancelled_at', 'cancel_reason', 'metadata']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'avatar', 'bio', 'quick_access_token', 'last_login_at', 
                    'last_login_ip', 'preferences'
                ]);
            });
        }
    }
};