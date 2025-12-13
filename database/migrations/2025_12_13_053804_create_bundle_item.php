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
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('cabang_resto_id')->nullable();

            $table->string('name', 100);
            $table->decimal('bundle_price', 16, 2);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('cabang_resto_id')->references('id')->on('cabang_resto')->nullOnDelete();
        });
        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_bundle_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('qty')->default(1);

            $table->timestamps();

            $table->foreign('product_bundle_id')
                ->references('id')->on('product_bundles')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->unique(['product_bundle_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('product_bundle_items');

    }
};
