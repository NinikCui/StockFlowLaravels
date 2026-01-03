<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

companyCabangBreadcrumbs();
companyWarehouseBreadcrumbs();

function companyCabangBreadcrumbs()
{
    // Company → Daftar Cabang
    Breadcrumbs::for('company.cabang.index', function (
        BreadcrumbTrail $trail,
        string $companyCode
    ) {
        $trail->push(
            'Cabang Restoran',
            route('cabang.index', ['companyCode' => $companyCode])
        );
    });

    // Company → Detail Cabang
    Breadcrumbs::for('company.cabang.show', function (
        BreadcrumbTrail $trail,
        string $companyCode,
        $cabang
    ) {
        $trail->parent('company.cabang.index', $companyCode);

        $trail->push(
            $cabang->name,
            route('cabang.detail', [
                'companyCode' => $companyCode,
                'code' => $cabang->code,
            ])
        );
    });

    // Company → Buat Cabang
    Breadcrumbs::for('company.cabang.create', function (
        BreadcrumbTrail $trail,
        string $companyCode
    ) {
        $trail->parent('company.cabang.index', $companyCode);

        $trail->push('Buat Cabang Baru');
    });

    // Company → Edit Cabang
    Breadcrumbs::for('company.cabang.edit', function (
        BreadcrumbTrail $trail,
        string $companyCode,
        $cabang
    ) {
        $trail->parent('company.cabang.show', $companyCode, $cabang);

        $trail->push('Edit Cabang');
    });
}
function companyWarehouseBreadcrumbs()
{
    // Company → Daftar Gudang
    Breadcrumbs::for('company.warehouse.index', function (
        BreadcrumbTrail $trail,
        string $companyCode
    ) {
        $trail->push(
            'Penyimpanan',
            route('warehouse.index', ['companyCode' => $companyCode])
        );
    });
    Breadcrumbs::for('company.warehouse.create', function (
        BreadcrumbTrail $trail,
        string $companyCode
    ) {
        $trail->parent('company.warehouse.index', $companyCode);
        $trail->push('Buat Gudang Baru');
    });

    Breadcrumbs::for('company.warehouse.detail', function (
        BreadcrumbTrail $trail,
        $companyCode,
        $warehouse
    ) {
        $trail->parent('company.warehouse.index', $companyCode);
        $trail->push($warehouse->name,
            route('warehouse.show', [
                'companyCode' => $companyCode,
                'code' => $warehouse->code,
                'warehouseId' => $warehouse->id,
            ]));
    });
    Breadcrumbs::for('company.warehouse.edit', function (
        BreadcrumbTrail $trail,
        $companyCode,
        $warehouse
    ) {
        $trail->parent('company.warehouse.detail', $companyCode, $warehouse);
        $trail->push('Edit Gudang');
    });
    Breadcrumbs::for('company.warehouse.detail.item-history', function (
        BreadcrumbTrail $trail,
        $companyCode,
        $warehouse, $stock,
    ) {
        $trail->parent('company.warehouse.detail', $companyCode, $warehouse);
        $trail->push('Stock '.$stock->code.' History');
    });
    Breadcrumbs::for('company.warehouse.stock-in.create', function (
        BreadcrumbTrail $trail,
        $companyCode,
        $warehouse,
    ) {
        $trail->parent('company.warehouse.detail', $companyCode, $warehouse);
        $trail->push('Tambah Stok');
    });
}
