<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Vendor\Enums\StoreNoticePriority;
use Modules\Vendor\Enums\StoreNoticeType;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('store_notices', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->enum('priority', StoreNoticePriority::getValues())->default(StoreNoticePriority::LOW);
            $table->text('notice');
            $table->text('description')->nullable();
            $table->dateTime('effective_from')->default(now());
            $table->dateTime('expired_at');
            $table->enum('type', StoreNoticeType::getValues());
            $table->foreignUuid('created_by')->nullable()->references('id')->on('users');
            $table->foreignUuid('updated_by')->nullable()->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('store_notice_user', function (Blueprint $table): void {
            $table->foreignUuid('store_notice_id')->nullable()->references('id')->on('store_notices')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
        });
        Schema::create('store_notice_shop', function (Blueprint $table): void {
            $table->foreignUuid('store_notice_id')->nullable()->references('id')->on('store_notices')->cascadeOnDelete();
            $table->foreignUuid('shop_id')->nullable()->references('id')->on('shops')->cascadeOnDelete();
        });
        Schema::create('store_notice_read', function (Blueprint $table): void {
            $table->foreignUuid('store_notice_id')->nullable()->references('id')->on('store_notices')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_notices');
        Schema::dropIfExists('store_notice_user');
        Schema::dropIfExists('store_notice_shop');
        Schema::dropIfExists('store_notice_read');
    }
};
