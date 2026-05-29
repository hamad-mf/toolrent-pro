<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureGateAndLimitsTest extends TestCase
{
    use RefreshDatabase;

    private function role(string $slug): Role
    {
        return Role::create([
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
        ]);
    }

    private function userFor(Tenant $tenant, string $roleSlug): User
    {
        return User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => $roleSlug . '@example.test',
            'password' => 'password',
            'role_id' => $this->role($roleSlug)->id,
            'is_active' => true,
        ]);
    }

    public function test_disabled_features_are_blocked_even_when_the_route_is_known(): void
    {
        $tenant = Tenant::create([
            'name' => 'Test Shop',
            'slug' => 'test-shop',
            'features' => ['tools'],
        ]);

        $user = $this->userFor($tenant, Role::MANAGER);

        $this->actingAs($user)
            ->get(route('shop-admin.reports.index'))
            ->assertForbidden();
    }

    public function test_tool_creation_respects_the_tenant_tool_limit(): void
    {
        $tenant = Tenant::create([
            'name' => 'Small Shop',
            'slug' => 'small-shop',
            'max_tools' => 1,
            'features' => ['tools', 'categories'],
        ]);
        $user = $this->userFor($tenant, Role::SHOP_ADMIN);

        session(['tenant_id' => $tenant->id]);
        $category = Category::create(['name' => 'Drills', 'slug' => 'drills']);
        Tool::create([
            'category_id' => $category->id,
            'name' => 'Hammer Drill',
            'daily_rate' => 12,
            'status' => 'Available',
        ]);

        $this->actingAs($user)
            ->post(route('shop-admin.tools.store'), [
                'category_id' => $category->id,
                'name' => 'Impact Driver',
                'daily_rate' => 10,
                'status' => 'Available',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, Tool::count());
    }
}
