<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateTwoCompanyUsersSeeder extends Seeder
{
    public function run()
    {
        // ==========================================================
        // COMPANY 1
        // ==========================================================
        $company1 = Company::create([
            'name' => 'Company Satu',
            'code' => 'COMP1',
        ]);

        // ROLE OWNER UNTUK COMPANY 1
        $role1 = Role::create([
            'company_id' => $company1->id,
            'cabang_resto_id' => null,
            'code' => 'OWNER',
            'name' => 'OWNER_COMP1',   // harus UNIQUE per Spatie
            'guard_name' => 'web',
        ]);

        // ROLE OWNER mendapatkan SEMUA permission
        $role1->syncPermissions(Permission::all());

        // USER 1
        $user1 = User::create([
            'username' => 'nico',
            'email' => 'nico@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('niconico'),
            'is_active' => true,
        ]);

        // Assign ROLE (pakai NAME, not ID)
        $user1->assignRole($role1->name);

        // ==========================================================
        // COMPANY 2
        // ==========================================================
        $company2 = Company::create([
            'name' => 'Company Dua',
            'code' => 'COMP2',
        ]);

        // ROLE OWNER UNTUK COMPANY 2
        $role2 = Role::create([
            'company_id' => $company2->id,
            'cabang_resto_id' => null,
            'code' => 'OWNER',
            'name' => 'OWNER_COMP2',   // unik
            'guard_name' => 'web',
        ]);

        $role2->syncPermissions(Permission::all());

        // USER 2
        $user2 = User::create([
            'username' => 'nico1',
            'email' => 'nico1@example.com',
            'phone' => '089876543210',
            'password' => Hash::make('nico1nico1'),
            'is_active' => true,
        ]);

        $user2->assignRole($role2->name);

        echo "Seeder sukses: 2 company + 2 user + 2 owner role dibuat.\n";
    }
}
