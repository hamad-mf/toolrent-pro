<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => Role::SUPER_ADMIN],
            ['name' => 'Shop Admin', 'slug' => Role::SHOP_ADMIN],
            ['name' => 'Manager', 'slug' => Role::MANAGER],
            ['name' => 'Counter Staff', 'slug' => Role::COUNTER_STAFF],
            ['name' => 'Floor Staff', 'slug' => Role::FLOOR_STAFF],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
