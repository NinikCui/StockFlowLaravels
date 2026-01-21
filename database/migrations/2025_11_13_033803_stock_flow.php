<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================
        // COMPANIES
        // ============================
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 145);
            $table->string('code', 45)->unique();
            $table->string('timezone', 64)->nullable();
            $table->string('tax_id', 45)->nullable();
            $table->timestamps();
        });

        // ============================
        // USERS
        // (Tanpa roles_id, karena pakai Spatie)
        // ============================
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->boolean('is_active')->default(1);

            $table->unsignedBigInteger('company_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->nullOnDelete();
        });

        // ============================
        // SESSIONS & PASSWORD RESET
        // ============================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ============================
        // CABANG RESTO
        // ============================
        Schema::create('cabang_resto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');

            $table->string('name', 45);
            $table->string('code', 45);
            $table->string('address', 145);
            $table->string('city', 145);
            $table->string('phone', 45);
            $table->boolean(column: 'utama')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('manager_user_id')->nullable();

            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();

            $table->foreign('manager_user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        // ============================
        // WAREHOUSE TYPES
        // ============================
        Schema::create('warehouse_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name', 50);
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();
        });

        // ============================
        // WAREHOUSE
        // ============================
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_resto_id');
            $table->string('name', 45);
            $table->string('code', 45);
            $table->unsignedBigInteger('warehouse_type_id')->nullable();
            $table->timestamps();

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->cascadeOnDelete();

            $table->foreign('warehouse_type_id')
                ->references('id')->on('warehouse_types')
                ->nullOnDelete();
        });

        // ============================
        // SATUAN
        // ============================
        Schema::create('satuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();
        });

        // ============================
        // CATEGORIES
        // ============================
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();
        });

        // ============================
        // SUPPLIERS
        // ============================
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->unsignedBigInteger('cabang_resto_id')->nullable();
            $table->string('name', 100);
            $table->string('contact_name', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->cascadeOnDelete();
        });

        // ============================
        // ITEMS
        // ============================
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('satuan_id');

            $table->string('name', 45);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->boolean('forecast_enabled')->default(false);
            $table->boolean('mudah_rusak')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('satuan_id')->references('id')->on('satuan')->cascadeOnDelete();
        });

        // ============================
        // STOCKS
        // ============================
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('qty', 14, 2)->default(0);
            $table->date('expired_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // ============================
        // STOCK MOVEMENTS
        // ============================
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('created_by');

            $table->enum('type', ['IN', 'OUT', 'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT']);
            $table->decimal('qty', 14, 2);

            $table->string('reference')->nullable();
            $table->string('notes')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreign('stock_id')->references('id')->on('stocks')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });

        // ============================
        // SUPPLIER ITEM (PIVOT)
        // ============================
        Schema::create('suppliers_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suppliers_id');
            $table->unsignedBigInteger('items_id');

            $table->decimal('price', 16, 4);
            $table->decimal('min_order_qty', 16, 4);
            $table->timestamp('last_price_update');

            $table->timestamps();

            $table->foreign('suppliers_id')->references('id')->on('suppliers')->cascadeOnDelete();
            $table->foreign('items_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // ============================
        // SUPPLIER SCORES
        // ============================
        Schema::create('supplier_scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('suppliers_id');

            $table->decimal('on_time_rate', 5, 2)->nullable();
            $table->decimal('reject_rate', 5, 2)->nullable();
            $table->decimal('avg_quality', 6, 3)->nullable();
            $table->decimal('price_variance', 6, 3)->nullable();
            $table->string('notes', 245)->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->integer('period_month')->nullable();
            $table->integer('period_year')->nullable();

            $table->foreign('suppliers_id')->references('id')->on('suppliers')->cascadeOnDelete();
        });

        // ============================
        // PURCHASE ORDER
        // ============================
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('suppliers_id');

            $table->date('po_date');
            $table->enum('status', ['DRAFT', 'APPROVED', 'RECEIVED', 'CANCELLED']);

            $table->string('note', 200);
            $table->boolean('ontime');

            $table->string('po_number', 45)->nullable();
            $table->date('delivered_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('cabang_resto_id')->references('id')->on('cabang_resto')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreign('suppliers_id')->references('id')->on('suppliers')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // ============================
        // PO DETAIL
        // ============================
        Schema::create('po_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('items_id');

            $table->decimal('qty_ordered', 16, 4);
            $table->decimal('unit_price', 16, 4);
            $table->decimal('quality', 6, 3);

            $table->decimal('conversion_to_stock', 16, 4)->nullable();
            $table->decimal('discount_pct', 5, 2)->nullable();
            $table->string('note_line', 200)->nullable();

            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_order')->cascadeOnDelete();
            $table->foreign('items_id')->references('id')->on('items')->cascadeOnDelete();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // FK ke company
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name', 45);
            $table->string('code', 45);

            $table->decimal('base_price', 16, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // FK constraints
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            // product code harus unik dalam 1 company
            $table->unique(['company_id', 'code']);
        });

        Schema::create('boms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('item_id');

            $table->decimal('qty_per_unit', 16, 2);

            // FK
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->foreign('item_id')
                ->references('id')->on('items')
                ->cascadeOnDelete();

            // prevent dupes
            $table->unique(['product_id', 'item_id']);
        });

        // ============================
        // PO RECEIVE
        // ============================
        Schema::create('po_receive', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('received_by');

            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_order')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->cascadeOnDelete();
            $table->foreign('received_by')->references('id')->on('users')->cascadeOnDelete();
        });

        // ============================
        // PO RECEIVE DETAIL
        // ============================
        Schema::create('po_receive_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('po_receive_id');
            $table->unsignedBigInteger('po_detail_id');
            $table->unsignedBigInteger('item_id');

            $table->decimal('qty_received', 16, 4)->default(0);
            $table->decimal('qty_returned', 16, 4)->default(0);

            $table->string('note', 200)->nullable();
            $table->timestamps();

            $table->foreign('po_receive_id')->references('id')->on('po_receive')->cascadeOnDelete();
            $table->foreign('po_detail_id')->references('id')->on('po_detail')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_receive_detail');
        Schema::dropIfExists('po_receive');
        Schema::dropIfExists('boms');
        Schema::dropIfExists('products');
        Schema::dropIfExists('po_detail');
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('supplier_scores');
        Schema::dropIfExists('suppliers_item');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('satuan');
        Schema::dropIfExists('warehouse');
        Schema::dropIfExists('warehouse_types');
        Schema::dropIfExists('cabang_resto');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');
    }
};
