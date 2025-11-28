<?php

return [

    'COMPANY' => [

        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard/company',
            'always_show' => true,
        ],
        [
            'label' => 'Cabang Restoran',
            'icon' => 'store',
            'children' => [
                ['label' => 'Daftar Cabang', 'href' => 'cabang', 'permission' => 'branch.view'],
                ['label' => 'Gudang',        'href' => 'gudang', 'permission' => 'warehouse.view'],
            ],
        ],
        [
            'label' => 'Pegawai',
            'icon' => 'users',
            'children' => [
                ['label' => 'Daftar Pegawai', 'href' => 'pegawai', 'permission' => 'employee.view'],
                ['label' => 'Roles',          'href' => 'roles', 'permission' => 'permission.view'],
            ],
        ],
        [
            'label' => 'Produk',
            'icon' => 'box',
            'children' => [
                ['label' => 'Barang Baku', 'href' => 'items', 'permission' => 'item.view'],
            ],
        ],

        [
            'label' => 'Pembelian',
            'icon' => 'cart',
            'children' => [
                ['label' => 'Purchase Order', 'href' => 'purchase-order', 'permission' => 'purchase.view'],
                ['label' => 'Supplier',       'href' => 'supplier',       'permission' => 'supplier.view'],
            ],
        ],
        [
            'label' => 'Stok & Mutasi',
            'icon' => 'layers',
            'children' => [
                ['label' => 'Request Cabang', 'href' => 'request-cabang',                  'permission' => 'inventory.transfer'],
                ['label' => 'Analytics',      'href' => 'request-cabang/analytics/cabang', 'permission' => 'analytics.inventory'],
            ],
        ],
        [
            'label' => 'Pengaturan',
            'icon' => 'settings',
            'permission' => 'settings.general',
            'children' => [
                [
                    'label' => 'Umum',
                    'href' => 'settings/general',
                    'permission' => 'settings.general',
                ],
                [
                    'label' => 'Masalah',
                    'href' => 'settings/masalah',
                    'permission' => 'settings.general',
                ],
            ],
        ],

    ],
    'BRANCH' => [
        [
            'label' => 'Dashboard Cabang',
            'icon' => 'home',
            'href' => 'dashboard/branch',
            'always_show' => true,
        ],

        [
            'label' => 'Stok & Mutasi',
            'icon' => 'layers',
            'children' => [
                ['label' => 'Request Cabang', 'href' => 'request-cabang', 'permission' => 'request.view'],
                ['label' => 'Analytics',      'href' => 'request-cabang/analytics/cabang', 'permission' => 'analytics.view'],
            ],
        ],

        [
            'label' => 'Supplier',
            'icon' => 'truck',
            'children' => [
                ['label' => 'Daftar Supplier', 'href' => 'supplier', 'permission' => 'supplier.view'],
            ],
        ],

        [
            'label' => 'Produk',
            'icon' => 'box',
            'children' => [
                ['label' => 'Barang Baku', 'href' => 'items', 'permission' => 'items.view'],
            ],
        ],
    ],
];
