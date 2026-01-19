<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyWithOwnerSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $companyName = 'PT Rasa Nusantara';
            $companyCode = 'RASA';

            // Company
            $company = Company::create([
                'name' => $companyName,
                'code' => $companyCode,
            ]);

            // Owner User
            $user = User::create([
                'username' => 'owner_rasa',
                'email' => 'owner@rasanusantara.id',
                'phone' => '08123456789',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            // Role OWNER
            $role = Role::create([
                'company_id' => $company->id,
                'cabang_resto_id' => null,
                'code' => 'OWNER',
                'name' => 'OWNER_'.strtoupper($companyCode),
                'guard_name' => 'web',
            ]);

            // Full permission
            $role->syncPermissions(Permission::all());

            // Assign role
            $user->assignRole($role->name);
        });
    }
}
