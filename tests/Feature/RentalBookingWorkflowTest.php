<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalBookingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function makeStaff(Tenant $tenant): User
    {
        $role = Role::create(['name' => 'Counter Staff', 'slug' => Role::COUNTER_STAFF]);

        return User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Counter Staff',
            'email' => 'counter@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_booking_can_be_checked_out_and_returned(): void
    {
        $tenant = Tenant::create([
            'name' => 'Rental Shop',
            'slug' => 'rental-shop',
            'features' => ['tools', 'customers', 'rentals', 'invoicing'],
        ]);
        $staff = $this->makeStaff($tenant);

        session(['tenant_id' => $tenant->id]);
        $category = Category::create(['name' => 'Saws', 'slug' => 'saws']);
        $customer = Customer::create(['name' => 'Ava Customer', 'phone' => '555-0100']);
        $tool = Tool::create([
            'category_id' => $category->id,
            'name' => 'Circular Saw',
            'daily_rate' => 25,
            'status' => 'Available',
        ]);

        $this->actingAs($staff)
            ->post(route('shop-admin.rentals.store'), [
                'customer_id' => $customer->id,
                'tool_id' => $tool->id,
                'due_at' => now()->addDays(2)->format('Y-m-d'),
                'action_type' => 'booking',
                'deposit' => 10,
                'discount' => 0,
            ])
            ->assertSessionHas('success');

        $rental = Rental::first();
        $this->assertSame('Pending', $rental->status);
        $this->assertSame('Reserved', $tool->fresh()->status);

        $this->post(route('shop-admin.rentals.checkout', $rental))
            ->assertSessionHas('success');

        $this->assertSame('Active', $rental->fresh()->status);
        $this->assertSame('Rented', $tool->fresh()->status);

        $this->post(route('shop-admin.rentals.return', $rental))
            ->assertRedirect(route('shop-admin.rentals.index'));

        $this->assertSame('Returned', $rental->fresh()->status);
        $this->assertSame('Available', $tool->fresh()->status);
        $this->assertNotNull($rental->fresh()->total_price);
    }
}
