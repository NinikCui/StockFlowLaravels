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
        Schema::table('pos_payments', function (Blueprint $table) {
            $table->decimal('paid_amount', 16, 2)->nullable();
            $table->decimal('change_amount', 16, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_payments', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->dropColumn('change_amount');
        });
    }
};
