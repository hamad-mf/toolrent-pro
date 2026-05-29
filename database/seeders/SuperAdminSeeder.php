<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('slug', Role::SUPER_ADMIN)->first();

        User::updateOrCreate(
            ['email' => 'admin@toolrent.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role_id' => $role->id,
                'tenant_id' => null,
                'is_active' => true,
            ]
        );
    }
}
