<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBundle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBundleItemSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Ambil bundle
        $paketHemat = ProductBundle::where('name', 'Paket Hemat Nasi')->first();
        $paketKenyang = ProductBundle::where('name', 'Paket Kenyang')->first();
        $paketNgopi = ProductBundle::where('name', 'Paket Nongkrong')->first();

        // Ambil product
        $nasiGoreng = Product::where('code', 'MENU-NG')->first();
        $ayamGoreng = Product::where('code', 'MENU-NAG')->first();
        $esTeh = Product::where('code', 'MENU-ETM')->first();
        $kopiHitam = Product::where('code', 'MENU-KH')->first();
        $mieGoreng = Product::where('code', 'MENU-MG')->first();

        DB::table('product_bundle_items')->insert([

            // ================= Paket Hemat =================
            [
                'product_bundle_id' => $paketHemat->id,
                'product_id' => $nasiGoreng->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'product_bundle_id' => $paketHemat->id,
                'product_id' => $esTeh->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ================= Paket Kenyang =================
            [
                'product_bundle_id' => $paketKenyang->id,
                'product_id' => $nasiGoreng->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'product_bundle_id' => $paketKenyang->id,
                'product_id' => $ayamGoreng->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'product_bundle_id' => $paketKenyang->id,
                'product_id' => $esTeh->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // ================= Paket Nongkrong =================
            [
                'product_bundle_id' => $paketNgopi->id,
                'product_id' => $kopiHitam->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'product_bundle_id' => $paketNgopi->id,
                'product_id' => $mieGoreng->id,
                'qty' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
