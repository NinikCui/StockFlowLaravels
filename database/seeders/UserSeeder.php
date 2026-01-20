<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cabangs = CabangResto::all();

        foreach ($cabangs as $cabang) {

            // MANAGER
            $manager = User::create([
                'username' => 'manager_'.$cabang->id,
                'email' => 'manager'.$cabang->id.'@rasanusantara.id',
                'phone' => '08110000'.$cabang->id,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $manager->assignRole('MANAGER_'.strtoupper($cabang->name));

            // KASIR (2)
            for ($i = 1; $i <= 2; $i++) {
                $kasir = User::create([
                    'username' => "kasir_{$cabang->id}_{$i}",
                    'email' => "kasir{$cabang->id}_{$i}@rasanusantara.id",
                    'phone' => "081110{$cabang->id}{$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]);
                $kasir->assignRole('KASIR_'.strtoupper($cabang->name));
            }

            // GUDANG
            $gudang = User::create([
                'username' => 'gudang_'.$cabang->id,
                'email' => 'gudang'.$cabang->id.'@rasanusantara.id',
                'phone' => '08112000'.$cabang->id,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $gudang->assignRole('GUDANG_'.strtoupper($cabang->name));

            // KITCHEN (2)
            for ($i = 1; $i <= 2; $i++) {
                $kitchen = User::create([
                    'username' => "kitchen_{$cabang->id}_{$i}",
                    'email' => "kitchen{$cabang->id}_{$i}@rasanusantara.id",
                    'phone' => "081130{$cabang->id}{$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]);
                $kitchen->assignRole('KITCHEN_'.strtoupper($cabang->name));
            }

            // STAFF (2)
            for ($i = 1; $i <= 2; $i++) {
                $staff = User::create([
                    'username' => "staff_{$cabang->id}_{$i}",
                    'email' => "staff{$cabang->id}_{$i}@rasanusantara.id",
                    'phone' => "081140{$cabang->id}{$i}",
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]);
                $staff->assignRole('STAFF_'.strtoupper($cabang->name));
            }
        }
    }
}
