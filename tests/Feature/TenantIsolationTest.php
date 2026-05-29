<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function makeTenant(string $name): Tenant
    {
        return Tenant::create([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
        ]);
    }

    public function test_global_scope_only_returns_records_for_the_active_tenant(): void
    {
        $tenantA = $this->makeTenant('Tenant A');
        $tenantB = $this->makeTenant('Tenant B');

        // Seed a category for each tenant without a session active.
        Category::create(['tenant_id' => $tenantA->id, 'name' => 'Drills', 'slug' => 'drills']);
        Category::create(['tenant_id' => $tenantB->id, 'name' => 'Ladders', 'slug' => 'ladders']);

        // Acting as tenant A: only tenant A's categories should be visible.
        session(['tenant_id' => $tenantA->id]);
        $this->assertSame(1, Category::count());
        $this->assertEquals(['Drills'], Category::pluck('name')->all());

        // Switch to tenant B: only tenant B's categories should be visible.
        session(['tenant_id' => $tenantB->id]);
        $this->assertSame(1, Category::count());
        $this->assertEquals(['Ladders'], Category::pluck('name')->all());
    }

    public function test_created_records_are_stamped_with_the_active_tenant(): void
    {
        $tenant = $this->makeTenant('Tenant A');

        session(['tenant_id' => $tenant->id]);
        $category = Category::create(['name' => 'Saws', 'slug' => 'saws']);

        $this->assertEquals($tenant->id, $category->tenant_id);
    }

    public function test_records_from_other_tenants_are_not_accessible_by_id(): void
    {
        $tenantA = $this->makeTenant('Tenant A');
        $tenantB = $this->makeTenant('Tenant B');

        $categoryB = Category::create(['tenant_id' => $tenantB->id, 'name' => 'Ladders', 'slug' => 'ladders']);

        // Acting as tenant A, tenant B's record must not be findable.
        session(['tenant_id' => $tenantA->id]);
        $this->assertNull(Category::find($categoryB->id));
    }
}
