<?php

return [

    'COMPANY' => [

        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'href' => 'dashboard',
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
            'label' => 'Manajemen Produk',
            'icon' => 'box',
            'children' => [
                ['label' => 'Barang Baku', 'href' => 'items', 'permission' => 'item.view'],
                ['label' => 'Product', 'href' => 'products', 'permission' => 'item.view'],
                ['label' => 'Paket', 'href' => 'bundles', 'permission' => 'item.view'],
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
                [
                    'label' => 'Daftar Item',
                    'href' => 'itemmanage',
                    'permission' => 'item.view',
                ],
                [
                    'label' => 'Daftar Stok',
                    'href' => 'stockmanage',
                    'permission' => 'item.view',
                ],
                ['label' => 'Request Cabang', 'href' => 'request-cabang',                  'permission' => 'transfer.view'],
            ],
        ],
        /* [
            'label' => 'Laporan',
            'icon' => 'bar-chart',
            'permission' => 'report.view',
            'children' => [

                [
                    'label' => 'Stok Global',
                    'href' => 'reports/stock-global', // GET /reports/stock-global
                    'permission' => 'report.view',
                ],
                [
                    'label' => 'Mutasi Stok',
                    'href' => 'reports/mutasi-stok', // GET /reports/mutasi-stok
                    'permission' => 'report.view',
                ],
                [
                    'label' => 'Penggunaan Bahan (BOM)',
                    'href' => 'reports/bom', // GET /reports/bom
                    'permission' => 'report.view',
                ],
                [
                    'label' => 'Purchase Order',
                    'href' => 'reports/purchase-order', // GET /reports/purchase-order
                    'permission' => 'report.view',
                ],
                [
                    'label' => 'Hampir Expired',
                    'href' => 'reports/expired', // GET /reports/expired
                    'permission' => 'report.view',
                ],
                [
                    'label' => 'Performa Cabang',
                    'href' => 'reports/performance', // GET /reports/performance
                    'permission' => 'report.view',
                ],
            ],
        ],*/
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
            'href' => 'dashboard',
            'always_show' => true,
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
            'label' => 'Stok & Mutasi',
            'icon' => 'layers',
            'children' => [
                [
                    'label' => 'Gudang',
                    'href' => 'penyimpanan',
                    'permission' => 'warehouse.view',
                ],
                [
                    'label' => 'Daftar Item',
                    'href' => 'item',
                    'permission' => 'item.view',
                ],
                [
                    'label' => 'Daftar Stok',
                    'href' => 'stock',
                    'permission' => 'item.view',
                ],
                [
                    'label' => 'Request Barang',
                    'href' => 'request-cabang',
                    'permission' => 'transfer.view',
                ],
            ],
        ],
        [
            'label' => 'Supplier',
            'icon' => 'truck',
            'children' => [
                [
                    'label' => 'Daftar Supplier',
                    'href' => 'supplier',
                    'permission' => 'supplier.view',
                ],
                [
                    'label' => 'Purchase Order',
                    'href' => 'purchase-order',
                    'permission' => 'purchase.view',
                ],
            ],
        ],
        [
            'label' => 'Produk',
            'icon' => 'box',
            'children' => [
                ['label' => 'Product', 'href' => 'products', 'permission' => 'item.view'],
                ['label' => 'Paket', 'href' => 'bundles', 'permission' => 'item.view'],

            ],
        ],
        [
            'label' => 'Laporan',
            'icon' => 'bar-chart',
            'permission' => 'report.view',
            'children' => [
                [
                    'label' => 'Rekomendasi Menu',
                    'href' => 'menu-promotion',
                    'permission' => 'report.view',
                ],

            ],
        ],
        [
            'label' => 'POS',
            'icon' => 'home',
            'href' => 'pos-shift',
            'permission' => 'pos.manage',
        ],

    ],

];
