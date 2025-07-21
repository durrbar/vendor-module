<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attributes', function (Blueprint $table): void {
            $table->uuid('shop_id')->nullable()->after('name');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });

        Schema::table('attribute_values', function (Blueprint $table): void {
            $table->string('meta')->after('value')->nullable();
        });

        Schema::table('types', function (Blueprint $table): void {
            $table->json('settings')->after('name')->nullable();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->uuid('shop_id')->after('price')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->uuid('shop_id')->after('coupon_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->uuid('parent_id')->after('coupon_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('orders')->onDelete('cascade');
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
    }
};
