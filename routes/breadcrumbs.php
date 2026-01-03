<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

/*
|--------------------------------------------------------------------------
| COMPANY
|--------------------------------------------------------------------------
*/

// Company → Daftar Cabang
Breadcrumbs::for('company.cabang.index', function (BreadcrumbTrail $trail, $company) {
    $trail->push(
        'Cabang Restoran',
        route('cabang.index', ['companyCode' => $company])
    );

});

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
Breadcrumbs::for('company.cabang.create', function (BreadcrumbTrail $trail, $company) {
    $trail->parent('company.cabang.index', $company);

    $trail->push('Buat Cabang Baru');
});
Breadcrumbs::for('company.cabang.edit', function (
    BreadcrumbTrail $trail,
    string $companyCode,
    $cabang
) {
    $trail->parent('company.cabang.show', $companyCode, $cabang);

    // Label jelas & rapi
    $trail->push('Edit Cabang');
});
