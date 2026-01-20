<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryIssueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories_issues')->insert([

            [
                'company_id' => 1,
                'name' => 'Stok Habis',
                'desc' => 'Kondisi ketika stok bahan baku tidak tersedia atau habis saat dibutuhkan.',
            ],
            [
                'company_id' => 1,
                'name' => 'Stok Kurang',
                'desc' => 'Kondisi ketika jumlah stok berada di bawah batas minimum yang ditentukan.',
            ],
            [
                'company_id' => 1,
                'name' => 'Bahan Rusak',
                'desc' => 'Masalah pada bahan baku yang mengalami kerusakan, kedaluwarsa, atau tidak layak pakai.',
            ],
            [
                'company_id' => 1,
                'name' => 'Kesalahan Pencatatan',
                'desc' => 'Perbedaan antara stok fisik dengan data stok di dalam sistem.',
            ],
            [
                'company_id' => 1,
                'name' => 'Keterlambatan Supplier',
                'desc' => 'Pengiriman bahan baku dari supplier tidak sesuai dengan jadwal yang telah ditentukan.',
            ],
            [
                'company_id' => 1,
                'name' => 'Kualitas Bahan Tidak Sesuai',
                'desc' => 'Bahan baku yang diterima tidak sesuai dengan standar kualitas yang ditetapkan.',
            ],
            [
                'company_id' => 1,
                'name' => 'Kesalahan Distribusi Cabang',
                'desc' => 'Masalah dalam proses pemindahan atau distribusi stok antar cabang.',
            ],
            [
                'company_id' => 1,
                'name' => 'Kesalahan Proses Produksi',
                'desc' => 'Masalah yang terjadi saat proses pengolahan atau penggunaan bahan baku.',
            ],
        ]);
    }
}
