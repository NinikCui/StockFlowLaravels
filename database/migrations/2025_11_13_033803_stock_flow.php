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
        


        

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            
            $table->string('name', 145);
            $table->string('code', 45)->unique();
            $table->string('timezone', 64)->nullable();
            $table->string('tax_id', 45)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

        });

        

        Schema::create('roles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');  // FK to companies
            $table->string('name', 45);
            $table->string('code', 45);

            // cabang resto bisa NULL → role perusahaan / role global
            $table->unsignedBigInteger('cabang_resto_id')->nullable();

            // FK companies
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');

            

            // Unique per perusahaan + cabang + kode
            $table->unique(
                ['company_id', 'cabang_resto_id', 'code'],
                'uq_roles_company_branch_code'
            );

            // Indexing (biar query cepat)
            $table->index('company_id', 'fk_roles_companies1_idx');
            $table->index('cabang_resto_id', 'fk_roles_cabang_resto1_idx');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->boolean("is_active")->default(1);

            $table->unsignedBigInteger('roles_id');

            $table->foreign('roles_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            // Index (sesuai Prisma)
            $table->index('roles_id', 'fk_user_roles1_idx');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });


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

        Schema::create('cabang_resto', function (Blueprint $table) {
            $table->id();

            // FK ke companies
            $table->unsignedBigInteger('company_id');

            $table->string('name', 45);
            $table->string('code', 45);
            $table->string('address', 145);
            $table->string('city', 145);
            $table->string('phone', 45);

            $table->boolean('is_active')->default(true);

            // Lokasi (nullable)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Manager user (nullable)
            $table->unsignedBigInteger('manager_user_id')->nullable();

            // timestamps
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // FK: company
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');


            // index
            $table->index('company_id', 'fk_cabang_resto_company_idx');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->nullOnDelete();
        });

        Schema::table('cabang_resto', function (Blueprint $table) {
            $table->foreign('manager_user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->integer('company_id')->nullable();
            $table->string('code', 80);
            $table->string('resource', 80);
            $table->string('action', 40);
            $table->string('description', 200)->nullable();

            // ENUM
            $table->enum('scope', ['GLOBAL','COMPANY','BRANCH'])
                ->default('COMPANY');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // Unique & Index
            $table->unique(['company_id', 'code'], 'uq_permissions_company_code');
            $table->index('company_id', 'fk_permissions_companies1_idx');
        });

        

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('roles_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('cabang_resto_id')->nullable();

            $table->enum('effect', ['ALLOW','DENY'])
                ->default('ALLOW');

            // FK
            $table->foreign('roles_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->onDelete('cascade');

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->nullOnDelete();

            // Unique constraint
            $table->unique(
                ['roles_id','permission_id','cabang_resto_id'],
                'uq_role_perm_role_perm_branch'
            );

            // Index
            $table->index('roles_id', 'fk_role_permissions_roles1_idx');
            $table->index('permission_id', 'fk_role_permissions_permissions1_idx');
            $table->index('cabang_resto_id', 'fk_role_permissions_cabang1_idx');
        });

        Schema::create('user_permission_overrides', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('cabang_resto_id')->nullable();

            $table->enum('effect', ['ALLOW','DENY'])
                ->default('ALLOW')
                ->using('effect::permission_effect');

            // FK
            $table->foreign('users_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')->on('permissions')
                ->onDelete('cascade');

            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->nullOnDelete();

            // Unique
            $table->unique(
                ['users_id','permission_id','cabang_resto_id'],
                'uq_user_override_user_perm_branch'
            );

            // Index
            $table->index('users_id', 'fk_user_overrides_users1_idx');
            $table->index('permission_id', 'fk_user_overrides_permissions1_idx');
            $table->index('cabang_resto_id', 'fk_user_overrides_cabang1_idx');
        });
        Schema::create('warehouse_types', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('name', 50); 

            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');
        });
        Schema::create('warehouse', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cabang_resto_id');
            $table->string('name', 45);
            $table->string('code', 45);
            $table->unsignedBigInteger('warehouse_type_id')->nullable();

            
            $table->foreign('warehouse_type_id')
                ->references('id')->on('warehouse_types')
                ->onDelete('set null');
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');
                
            $table->index('cabang_resto_id', 'fk_werehouse_cabang_resto1_idx');
        });
        
        Schema::create('satuan', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('name');
            $table->string('code')->unique(); // KG, GR, PCS
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->cascadeOnDelete();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Tenant-level (company scoped)
            $table->unsignedBigInteger('company_id');

            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign key
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('name', 100);         // nama supplier (wajib)
            $table->string('contact_name', 100)->nullable(); // PIC
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

            $table->index('company_id');
            $table->index('name');
        });
     


        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // Tenant scope
            $table->unsignedBigInteger('company_id');

            // Relasi utama
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('satuan_id');

            // Field item
            $table->string('name', 45);
            $table->boolean('mudah_rusak')->default(false);      
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->boolean('forecast_enabled')->default(false);


            $table->timestamps();

            // FK
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->cascadeOnDelete();

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->cascadeOnDelete();

            $table->foreign('satuan_id')
                ->references('id')->on('satuan')
                ->cascadeOnDelete();

            // Indexes untuk optimasi
            $table->index('company_id');
            $table->index('category_id');
            $table->index('satuan_id');
        });

            Schema::create('stocks', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('warehouse_id');
                $table->unsignedBigInteger('item_id');

                $table->decimal('qty', 14, 2)->default(0);

                $table->timestamps();

                // Relasi
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouse')->onDelete('cascade');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

                // Unique per item per gudang
                $table->unique(['warehouse_id', 'code']);
            });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('stock_id');   // ⭐ BARU → wajib untuk multi-stok
            $table->unsignedBigInteger('item_id');    // tetap ada untuk filter cepat
            $table->unsignedBigInteger('created_by');

            // Movement type
            $table->enum('type', [
                'IN', 
                'OUT', 
                'TRANSFER_IN', 
                'TRANSFER_OUT', 
                'ADJUSTMENT'
            ]);

            // Qty bisa plus / minus
            $table->decimal('qty', 14, 2);

            // Optional reference (PO, Transfer, Adjustment ID)
            $table->string('reference')->nullable();

            // Catatan opsional
            $table->string('notes')->nullable();

            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouse')->onDelete('cascade');

            // ⭐ FK baru untuk menghubungkan movement ke stok tertentu
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');

            // items FK tidak dihapus, karena penting untuk filter cepat
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            // Index untuk optimasi
            $table->index(['warehouse_id', 'stock_id', 'item_id', 'type']);
        });
        Schema::create('suppliers_item', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('suppliers_id');
            $table->unsignedBigInteger('items_id');

            $table->decimal('price', 16, 4);
            $table->decimal('min_order_qty', 16, 4);
            $table->timestamp('last_price_update');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('suppliers_id')
                ->references('id')->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            $table->index('suppliers_id', 'fk_suppliers_item_suppliers1_idx');
            $table->index('items_id', 'fk_suppliers_item_items1_idx');
        });

        Schema::create('supplier_scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('suppliers_id');

            $table->decimal('on_time_rate', 5, 2)->nullable();
            $table->decimal('reject_rate', 5, 2)->nullable();
            $table->decimal('avg_quality', 6, 3)->nullable();
            $table->decimal('price_variance', 6, 3)->nullable();
            $table->string('notes', 245)->nullable();
            $table->timestamp('calculated_at')->useCurrent();

            $table->foreign('suppliers_id')
                ->references('id')->on('suppliers')
                ->onDelete('cascade');

            $table->index('suppliers_id', 'fk_supplier_scores_suppliers1_idx');
        });

        

        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cabang_resto_id');
            $table->unsignedBigInteger('suppliers_id');

            $table->date('po_date');
            $table->enum('status', [
                'DRAFT','APPROVED','PARTIAL','RECEIVED','CANCELLED'
            ]);

            $table->string('note', 200);
            $table->boolean('ontime');

            $table->string('po_number', 45)->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // FK
            $table->foreign('cabang_resto_id')
                ->references('id')->on('cabang_resto')
                ->onDelete('cascade');

            $table->foreign('suppliers_id')
                ->references('id')->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->nullOnDelete();

            // Index
            $table->index('cabang_resto_id', 'fk_purchase_order_cabang_resto1_idx');
            $table->index('suppliers_id', 'fk_purchase_order_suppliers1_idx');
        });

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

            // FK
            $table->foreign('purchase_order_id')
                ->references('id')->on('purchase_order')
                ->onDelete('cascade');

            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            // Index
            $table->index('purchase_order_id', 'fk_po_detail_purchase_order1_idx');
            $table->index('items_id', 'fk_po_detail_items1_idx');
        });

        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('po_detail_id');
            $table->timestamp('purchase_returns');
            $table->string('reason', 200);
            $table->unsignedBigInteger('created_by');
            $table->decimal('qty_returned', 16, 4);
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // FK
            $table->foreign('po_detail_id')
                ->references('id')->on('po_detail')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // Index
            $table->index('po_detail_id', 'fk_purchase_returns_po_detail1_idx');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 45);
            $table->string('code', 45)->unique();
            $table->decimal('base_price', 16, 2);
            $table->boolean('is_active');

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('boms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('products_id');
            $table->unsignedBigInteger('items_id');

            $table->decimal('qty_per_unit', 16, 2);

            // FK
            $table->foreign('products_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('items_id')
                ->references('id')->on('items')
                ->onDelete('cascade');

            // Index
            $table->index('products_id', 'fk_boms_products1_idx');
            $table->index('items_id', 'fk_boms_items1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_permission_overrides');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('cabang_resto');
        Schema::dropIfExists('warehouse');
        Schema::dropIfExists('warehouse_type_id');
        Schema::dropIfExists('satuan');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('items');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('suppliers_item');
        Schema::dropIfExists('supplier_scores');
        Schema::dropIfExists('purchase_order');
        Schema::dropIfExists('po_detail');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('products');
        Schema::dropIfExists('boms');
    }
};
