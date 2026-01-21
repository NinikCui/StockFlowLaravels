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

        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('opened_by');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('opened_at');
            $table->decimal('opening_cash', 16, 2);
            $table->timestamp('closed_at')->nullable();
            $table->decimal('closing_cash', 16, 2)->nullable();

            $table->enum('status', ['OPEN', 'CLOSED'])
                ->default('OPEN');

            $table->string('note', 200)->nullable();

            // FK
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            $table->foreign('opened_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('closed_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            // Index
            $table->index('cabang_resto_id', 'fk_pos_shifts_cabang_resto1_idx');
        });

        Schema::create('pos_order', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');
            $table->timestamp('order_datetime');

            $table->enum('status', ['OPEN', 'PAID', 'VOID']);

            $table->string('order_number', 45)->nullable();

            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('pos_shifts_id');

            $table->string('table_no', 20)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // FK
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            $table->foreign('pos_shifts_id')
                ->references('id')->on('pos_shifts')
                ->onDelete('cascade');

            $table->foreign('cashier_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // Index
            $table->index('cabang_resto_id', 'fk_pos_order_cabang_resto1_idx');
            $table->index('pos_shifts_id', 'fk_pos_order_pos_shifts1_idx');
        });

        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pos_order_id');
            $table->enum('method', ['CASH', 'QRIS', 'CARD', 'TRANSFER', 'OTHER']);

            $table->decimal('amount', 16, 2);
            $table->string('ref_number', 80)->nullable();
            $table->timestamp('paid_at');

            $table->enum('status', [
                'PENDING', 'SUCCESS', 'FAILED', 'REFUNDED',
            ])->default('SUCCESS');

            $table->string('note', 200)->nullable();

            // FK
            $table->foreign('pos_order_id')
                ->references('id')->on('pos_order')
                ->onDelete('cascade');

            // Index
            $table->index('pos_order_id', 'fk_pos_payments_pos_order1_idx');
        });

        Schema::create('order_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pos_order_id');
            $table->unsignedBigInteger('products_id');

            $table->integer('qty');
            $table->decimal('price', 16, 4);
            $table->decimal('discount_pct', 5, 2)->nullable();
            $table->string('note_line', 200)->nullable();

            // FK
            $table->foreign('pos_order_id')
                ->references('id')->on('pos_order')
                ->onDelete('cascade');

            $table->foreign('products_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            // Index
            $table->index('pos_order_id', 'fk_order_detail_pos_order1_idx');
            $table->index('products_id', 'fk_order_detail_products1_idx');
        });

        Schema::create('inven_trans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_id_to')->nullable();
            $table->unsignedBigInteger('cabang_id_from')->nullable();

            $table->string('trans_number', 45);
            $table->date('trans_date');

            $table->enum('status', [
                'REQUESTED', 'APPROVED', 'IN_TRANSIT', 'RECEIVED', 'CANCELLED', 'DRAFT', 'REJECTED',
            ])->default('DRAFT');

            $table->text('note')->nullable();
            $table->string('reason', 125)->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // FK
            $table->foreign('cabang_id_to')
                ->references('id')->on('cabang_resto')
                ->nullOnDelete();

            $table->foreign('cabang_id_from')
                ->references('id')->on('cabang_resto')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // index
            $table->index('cabang_id_from', 'fk_inventory_trans_cabang2_idx');
            $table->index('cabang_id_to', 'fk_inventory_trans_cabang1_idx');
        });

        Schema::create('inven_trans_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('items_id');
            $table->unsignedBigInteger('inven_trans_id');
            $table->decimal('qty', 16, 4);
            $table->text('note')->nullable();

            // FK
            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            $table->foreign('inven_trans_id')
                ->references('id')->on('inven_trans')
                ->onDelete('cascade');

            // Index
            $table->index('items_id', 'fk_inven_trans_items_id1_idx');
            $table->index('inven_trans_id', 'fk_inven_trans_inven_trans1_idx');
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number', 45);
            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('products_id');

            $table->decimal('qty_planned', 16, 4);
            $table->date('due_date');

            $table->enum('status', [
                'DRAFT', 'APPROVED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED',
            ])->default('DRAFT');

            $table->string('note', 245)->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            // FK
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->onDelete('cascade');

            $table->foreign('products_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // Index
            $table->index('cabang_resto_id', 'fk_production_orders_cabang_resto1_idx');
            $table->index('warehouse_id', 'fk_production_orders_warehouse1_idx');
            $table->index('products_id', 'fk_production_orders_products1_idx');
        });

        Schema::create('categories_issues', function (Blueprint $table) {
            $table->id();

            $table->string('name', 45);
            $table->text('desc');
            $table->unsignedBigInteger('company_id');

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');

        });
        Schema::create('production_issues', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('production_orders_id');
            $table->timestamp('issue_datetime');

            $table->enum('status', ['DRAFT', 'POSTED', 'CANCELLED'])
                ->default('DRAFT');

            $table->text('note');
            $table->unsignedBigInteger('categories_issues_id');

            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // FK
            $table->foreign('production_orders_id')
                ->references('id')->on('production_orders')
                ->onDelete('cascade');

            $table->foreign('categories_issues_id')
                ->references('id')->on('categories_issues')
                ->onDelete('cascade');

            // Index
            $table->index('production_orders_id', 'fk_production_issues_production_orders1_idx');
            $table->index('categories_issues_id', 'fk_production_issues_categories_issues1_idx');
        });

        Schema::create('production_issues_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('stocks_id');
            $table->unsignedBigInteger('production_issues_id');

            $table->decimal('qty_issues', 16, 4);

            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();

            // FK
            $table->foreign('stocks_id')
                ->references('id')->on('stocks')
                ->onDelete('cascade');

            $table->foreign('production_issues_id')
                ->references('id')->on('production_issues')
                ->onDelete('cascade');

            // Index
            $table->index('production_issues_id', 'fk_production_issues_detail_production_issues1_idx');
            $table->index('stocks_id', 'fk_production_issues_detail_stocks1_idx');
        });

        Schema::create('stocks_adjustmens', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('categories_issues_id');

            $table->timestamp('adjustment_date')->nullable();

            // enum ProductionStatus?  (DRAFT | POSTED | CANCELLED)
            $table->enum('status', ['DRAFT', 'POSTED', 'CANCELLED'])
                ->nullable()
                ->using('status::production_status');

            $table->string('note', 200)->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // optional link ke satu Stock
            $table->unsignedBigInteger('stockId')->nullable();

            // FK
            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->onDelete('cascade');

            $table->foreign('categories_issues_id')
                ->references('id')->on('categories_issues')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('stockId')
                ->references('id')->on('stocks')
                ->nullOnDelete();

            // Index
            $table->index('warehouse_id', 'fk_stocks_adjustmens_warehouse1_idx');
            $table->index('categories_issues_id', 'fk_stocks_adjustmens_categories_issues1_idx');
        });

        Schema::create('stocks_adjustmens_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('stocks_adjustmens_id');
            $table->unsignedBigInteger('stocks_id');

            $table->decimal('prev_qty', 16, 4);
            $table->decimal('after_qty', 16, 4);

            // FK
            $table->foreign('stocks_adjustmens_id')
                ->references('id')->on('stocks_adjustmens')
                ->onDelete('cascade');

            $table->foreign('stocks_id')
                ->references('id')->on('stocks')
                ->onDelete('cascade');

            // Index
            $table->index(
                'stocks_adjustmens_id',
                'fk_stocks_adjustmens_detail_stocks_adjustmens1_idx'
            );
            $table->index('stocks_id', 'fk_stocks_adjustmens_detail_stocks1_idx');
        });

        Schema::create('demand_daily', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('items_id');

            $table->date('date');
            $table->decimal('sales_qty', 16, 4);
            $table->decimal('demand_qty', 16, 4);

            $table->string('computed_from', 45)->default('POS+BOM');
            $table->timestamp('created_at')->useCurrent();

            // FK
            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->nullOnDelete();

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            // Index
            $table->index('cabang_resto_id', 'fk_demand_daily_cabang_resto1_idx');
            $table->index('warehouse_id', 'fk_demand_daily_warehouse1_idx');
            $table->index('items_id', 'fk_demand_daily_items1_idx');
        });

        Schema::create('restock_recomendations', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('items_id');

            $table->string('method', 45);
            $table->decimal('recommended_qty', 16, 4);
            $table->decimal('safety_stock', 16, 4)->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->string('reason', 245)->nullable();

            $table->enum('review_status', ['NEW', 'ACCEPTED', 'REJECTED', 'ORDERED'])
                ->default('NEW');

            $table->timestamp('created_at')->useCurrent();

            // FK
            $table->foreign('warehouse_id')
                ->references('id')->on('warehouse')
                ->onDelete('cascade');

            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            // Index
            $table->index('warehouse_id', 'fk_restock_recomendations_warehouse1_idx');
            $table->index('items_id', 'fk_restock_recomendations_items1_idx');
        });
        Schema::create('period_metrics', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');

            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('waste_rate', 6, 3)->nullable();
            $table->decimal('on_time_delivery', 6, 3)->nullable();
            $table->decimal('reject_rate', 6, 3)->nullable();
            $table->integer('stockout_events')->nullable();
            $table->decimal('service_level', 6, 3)->nullable();
            $table->decimal('turnover', 12, 3)->nullable();

            $table->json('extra_metrics')->nullable();
            $table->timestamp('computed_at')->useCurrent();

            // FK
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            // Index
            $table->index('cabang_resto_id', 'fk_period_metrics_cabang_resto1_idx');
        });

        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('from_satuan_id');
            $table->unsignedBigInteger('to_satuan_id');

            // nilai konversi
            // contoh: 1 KG = 1000 GR
            $table->decimal('factor', 15, 6);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // FK
            $table->foreign('from_satuan_id')
                ->references('id')->on('satuan')
                ->cascadeOnDelete();

            $table->foreign('to_satuan_id')
                ->references('id')->on('satuan')
                ->cascadeOnDelete();

            // mencegah duplikat
            $table->unique(
                ['from_satuan_id', 'to_satuan_id'],
                'unit_conversion_unique'
            );

            // index
            $table->index('from_satuan_id');
            $table->index('to_satuan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
        Schema::dropIfExists('pos_order');
        Schema::dropIfExists('pos_payments');
        Schema::dropIfExists('order_detail');
        Schema::dropIfExists('inven_trans');
        Schema::dropIfExists('inven_trans_detail');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('production_issues');
        Schema::dropIfExists('production_issues_detail');
        Schema::dropIfExists('categories_issues');
        Schema::dropIfExists('stocks_adjustmens');
        Schema::dropIfExists('stocks_adjustmens_detail');
        Schema::dropIfExists('demand_daily');
        Schema::dropIfExists('restock_recomendations');
        Schema::dropIfExists('period_metrics');
        Schema::dropIfExists('unit_conversions');

    }
};
