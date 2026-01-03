<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| COMPANY - CABANG
|--------------------------------------------------------------------------
*/

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
