<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_tenant_user_is_sent_back_to_login(): void
    {
        $tenant = Tenant::create([
            'name' => 'Paused Shop',
            'slug' => 'paused-shop',
            'is_active' => false,
        ]);
        $role = Role::create(['name' => 'Shop Admin', 'slug' => Role::SHOP_ADMIN]);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Paused Owner',
            'email' => 'paused@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('shop-admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }
}
