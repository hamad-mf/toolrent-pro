<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestTenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'testshop'],
            [
                'name' => 'Test Rental Shop',
                'primary_color' => '#ff5733',
                'plan' => 'Premium',
                'features' => ['categories', 'tools', 'customers', 'rentals', 'reports', 'invoicing', 'qrcode'],
            ]
        );

        $role = Role::where('slug', Role::SHOP_ADMIN)->first();

        // Important: When creating users for a tenant, we should probably disable the global scope 
        // if we are running this in a context where session('tenant_id') might be set.
        // But here in seeder, it's fine as session is empty.
        
        User::updateOrCreate(
            ['email' => 'shop@test.com'],
            [
                'name' => 'Shop Admin',
                'password' => Hash::make('shop123'),
                'role_id' => $role->id,
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ]
        );
    }
}
