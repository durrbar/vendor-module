<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Vendor\Enums\WithdrawStatus;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->constrained('users');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->json('cover_image')->nullable();
            $table->json('logo')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('address')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
        Schema::create('balances', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id')->constrained()->cascadeOnDelete();
            $table->double('admin_commission_rate')->nullable();
            $table->double('total_earnings')->default(0);
            $table->double('withdrawn_amount')->default(0);
            $table->double('current_balance')->default(0);
            $table->json('payment_info')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignUuid('shop_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('user_shop', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('withdraws', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id')->constrained()->cascadeOnDelete();
            $table->float('amount');
            $table->string('payment_method')->nullable();
            $table->enum('status', WithdrawStatus::getValues())->default(WithdrawStatus::PENDING);
            $table->text('details')->nullable();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('category_shop', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table): void {
                if (Schema::hasColumn('users', 'shop_id')) {
                    $table->dropForeign(['shop_id']);
                    $table->dropColumn('shop_id');
                }
            });
        }

        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('variation_options');
        Schema::dropIfExists('category_shop');
        Schema::dropIfExists('user_shop');
        Schema::dropIfExists('balances');
        Schema::dropIfExists('shops');
        // Schema::dropIfExists('tags');
        // Schema::dropIfExists('cards');
    }
};
