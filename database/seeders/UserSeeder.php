<?php

namespace Database\Seeders;

use App\Models\CabangResto;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $cabangs = CabangResto::all();

        // Manager per cabang
        foreach ($cabangs as $cabang) {
            $user = User::create([
                'username' => 'manager_'.$cabang->id,
                'email' => 'manager'.$cabang->id.'@rasanusantara.id',
                'phone' => '0812345678'.$cabang->id,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $user->assignRole('MANAGER_'.strtoupper($cabang->name));
        }

    }
}
