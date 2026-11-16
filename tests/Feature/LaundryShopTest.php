<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\LaundryShopSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryShopTest extends TestCase
{
    use RefreshDatabase;

    protected function seedShop(): void
    {
        $this->seed(LaundryShopSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_owner_can_login_and_see_dashboard(): void
    {
        $this->seedShop();

        $response = $this->post('/login', [
            'email' => 'owner@waterbonia.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->get('/')->assertOk()->assertSee('Dashboard');
    }

    public function test_staff_cannot_access_reports(): void
    {
        $this->seedShop();

        $staff = User::where('email', 'staff@waterbonia.test')->first();

        $this->actingAs($staff)->get('/reports')->assertForbidden();
        $this->actingAs($staff)->get('/services')->assertForbidden();
        $this->actingAs($staff)->get('/users')->assertForbidden();
    }

    public function test_order_creation_computes_total_fee(): void
    {
        $this->seedShop();

        $owner = User::where('email', 'owner@waterbonia.test')->first();
        $service = Service::where('name', 'Wash & Dry')->first();
        $customer = Customer::first();

        $this->actingAs($owner)->post('/orders', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'weight' => 5.5,
            'discount' => 10,
        ])->assertRedirect();

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertEquals(210.0, (float) $order->total_amount);
        $this->assertEquals('received', $order->status);
        $this->assertMatchesRegularExpression('/^WB-\d{8}-[A-F0-9]{4}$/', $order->order_number);
    }

    public function test_ready_for_pickup_sends_notifications(): void
    {
        $this->seedShop();

        $owner = User::where('email', 'owner@waterbonia.test')->first();
        $service = Service::first();
        $customer = Customer::first();

        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'weight' => 3,
            'price_per_kg' => $service->price_per_kg,
            'total_amount' => 3 * $service->price_per_kg,
            'status' => 'received',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->patch("/orders/{$order->id}/status", ['status' => 'ready_for_pickup'])
            ->assertRedirect();

        $order->refresh();

        $this->assertEquals('ready_for_pickup', $order->status);
        $this->assertTrue($order->notified);
        $this->assertGreaterThanOrEqual(1, Notification::where('order_id', $order->id)->count());
    }

    public function test_customer_can_track_order_publicly(): void
    {
        $this->seedShop();

        $owner = User::where('email', 'owner@waterbonia.test')->first();
        $service = Service::first();
        $customer = Customer::first();

        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'weight' => 4,
            'price_per_kg' => $service->price_per_kg,
            'total_amount' => 4 * $service->price_per_kg,
            'status' => 'drying',
            'received_at' => now(),
            'expected_pickup_at' => now()->addDays(2),
            'created_by' => $owner->id,
        ]);

        $this->get('/track')->assertOk()->assertSee('Where is my laundry?');

        $this->get('/track?order_number=' . $order->order_number)
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Drying');

        $this->get('/track?order_number=NOPE-0000')
            ->assertOk()
            ->assertSee('No matching order was found');

        $this->get('/track?phone=' . urlencode($customer->phone))
            ->assertOk()
            ->assertSee('Matching orders');
    }

    public function test_payment_updates_order_balance(): void
    {
        $this->seedShop();

        $owner = User::where('email', 'owner@waterbonia.test')->first();
        $service = Service::first();
        $customer = Customer::first();

        $order = Order::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'weight' => 4,
            'price_per_kg' => $service->price_per_kg,
            'total_amount' => 4 * $service->price_per_kg,
            'status' => 'received',
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->post("/orders/{$order->id}/payments", [
                'amount' => $order->total_amount,
                'amount_tendered' => $order->total_amount + 50,
                'method' => 'cash',
            ])
            ->assertRedirect(route('receipt.show', $order));

        $order->refresh();

        $this->assertTrue($order->is_paid);
        $this->assertEquals(50.0, (float) $order->payments->first()->change_due);
    }
}
