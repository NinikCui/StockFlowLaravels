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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_main_ingredient')
                ->default(false)
                ->after('mudah_rusak');
        });
        Schema::create('menu_promotion_recommendations', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('product_id');

            $table->decimal('risk_score', 6, 3);
            $table->integer('days_to_expired');
            $table->decimal('potential_usage', 16, 4);

            $table->string('reason', 255)->nullable();

            $table->enum('status', ['NEW', 'APPLIED', 'IGNORED'])
                ->default('NEW');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->cascadeOnDelete();

            $table->foreign('item_id')
                ->references('id')->on('items')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->index('date');
            $table->index('status');
            $table->index(['cabang_resto_id', 'date'], 'mpr_cabang_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('is_main_ingredient');
        });
        Schema::dropIfExists('menu_promotion_recommendations');

    }
};
