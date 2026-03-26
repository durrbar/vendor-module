<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table): void {
            $table->foreignUuid('shop_id')->nullable()->after('name')->constrained()->cascadeOnDelete();
        });

        Schema::table('attribute_values', function (Blueprint $table): void {
            $table->string('meta')->after('value')->nullable();
        });

        Schema::table('types', function (Blueprint $table): void {
            $table->json('settings')->after('name')->nullable();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignUuid('shop_id')->after('price')->nullable()->constrained()->cascadeOnDelete();
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignUuid('shop_id')->after('coupon_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('parent_id')->after('coupon_id')->nullable()->constrained('orders')->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');

        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table): void {
                if (Schema::hasColumn('attributes', 'shop_id')) {
                    $table->dropForeign(['shop_id']);
                    $table->dropColumn('shop_id');
                }
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table): void {
                if (Schema::hasColumn('products', 'shop_id')) {
                    $table->dropForeign(['shop_id']);
                    $table->dropColumn('shop_id');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table): void {
                if (Schema::hasColumn('orders', 'shop_id')) {
                    $table->dropForeign(['shop_id']);
                    $table->dropColumn('shop_id');
                }
                if (Schema::hasColumn('orders', 'parent_id')) {
                    $table->dropForeign(['parent_id']);
                    $table->dropColumn('parent_id');
                }
            });
        }
    }
};
