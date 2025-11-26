<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ================================
        // 1) PERMISSIONS (Spatie)
        // ================================
        Schema::table('permissions', function (Blueprint $table) {

            if (! Schema::hasColumn('permissions', 'code')) {
                $table->string('code')->nullable()->after('name');
            }

            if (! Schema::hasColumn('permissions', 'resource')) {
                $table->string('resource')->nullable()->after('code');
            }

            if (! Schema::hasColumn('permissions', 'action')) {
                $table->string('action')->nullable()->after('resource');
            }

            if (! Schema::hasColumn('permissions', 'scope')) {
                $table->enum('scope', ['GLOBAL', 'COMPANY', 'BRANCH'])
                    ->default('COMPANY')
                    ->after('action');
            }

        });

        // ================================
        // 2) ROLES (Spatie + custom RestoApp)
        // ================================
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('roles', 'cabang_resto_id')) {
                $table->unsignedBigInteger('cabang_resto_id')->nullable()->after('company_id');
            }

            if (! Schema::hasColumn('roles', 'code')) {
                $table->string('code', 45)->nullable()->after('name');
            }

            if (! Schema::hasColumn('roles', 'is_universal')) {
                $table->boolean('is_universal')->default(false)->after('code');
            }

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('cabang_resto_id')->references('id')->on('cabang_resto')->nullOnDelete();

            $table->index('company_id');
            $table->index('cabang_resto_id');
        });

        // ================================
        // 3) MODEL_HAS_ROLES
        // ================================
        Schema::table('model_has_roles', function (Blueprint $table) {
            if (! Schema::hasColumn('model_has_roles', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable();
            }

            if (! Schema::hasColumn('model_has_roles', 'cabang_resto_id')) {
                $table->unsignedBigInteger('cabang_resto_id')->nullable();
            }

            $table->index(['company_id', 'cabang_resto_id']);
        });

        // ================================
        // 4) MODEL_HAS_PERMISSIONS
        // ================================
        Schema::table('model_has_permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('model_has_permissions', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable();
            }

            if (! Schema::hasColumn('model_has_permissions', 'cabang_resto_id')) {
                $table->unsignedBigInteger('cabang_resto_id')->nullable();
            }

            $table->index(['company_id', 'cabang_resto_id']);
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'code', 'resource', 'action', 'scope']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'cabang_resto_id', 'code', 'is_universal']);
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'cabang_resto_id']);
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'cabang_resto_id']);
        });
    }
};
