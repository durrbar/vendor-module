<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Vendor\Enums\WithdrawStatus;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->foreign('owner_id')->references('id')->on('users');
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
            $table->uuid('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->double('admin_commission_rate')->nullable();
            $table->double('total_earnings')->default(0);
            $table->double('withdrawn_amount')->default(0);
            $table->double('current_balance')->default(0);
            $table->json('payment_info')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });

        Schema::create('user_shop', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('withdraws', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
            $table->uuid('shop_id');
            $table->uuid('category_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('variation_options');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('cards');
    }
};
