<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaundryShopSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner'],
            [
                'name' => 'Waterbonia Owner',
                'password' => 'owner123',
                'role' => 'owner',
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            ['email' => 'staff@waterbonia.test'],
            [
                'name' => 'Waterbonia Staff',
                'password' => 'password123',
                'role' => 'staff',
                'is_active' => true,
            ],
        );

        $services = [
            ['name' => 'Wash & Dry', 'price_per_kg' => 40, 'estimated_days' => 2, 'description' => 'Standard machine wash and dryer cycle.'],
            ['name' => 'Wash, Dry & Fold', 'price_per_kg' => 50, 'estimated_days' => 2, 'description' => 'Wash, dry and neatly folded laundry.'],
            ['name' => 'Wash, Dry, Fold & Iron', 'price_per_kg' => 60, 'estimated_days' => 3, 'description' => 'Full service including ironing.'],
            ['name' => 'Iron Only', 'price_per_kg' => 30, 'estimated_days' => 1, 'description' => 'Ironing service for pre-washed laundry.'],
            ['name' => 'Dry Clean', 'price_per_kg' => 80, 'estimated_days' => 3, 'description' => 'Dry cleaning for delicate garments.'],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service + ['is_active' => true]);
        }

        $items = [
            ['name' => 'Detergent Powder', 'category' => 'Cleaning', 'unit' => 'kg', 'quantity' => 25, 'reorder_level' => 10, 'unit_cost' => 95],
            ['name' => 'Fabric Conditioner', 'category' => 'Cleaning', 'unit' => 'gallon', 'quantity' => 8, 'reorder_level' => 4, 'unit_cost' => 180],
            ['name' => 'Bleach', 'category' => 'Cleaning', 'unit' => 'gallon', 'quantity' => 5, 'reorder_level' => 3, 'unit_cost' => 120],
            ['name' => 'Plastic Packaging', 'category' => 'Packaging', 'unit' => 'pcs', 'quantity' => 200, 'reorder_level' => 100, 'unit_cost' => 2.5],
            ['name' => 'Laundry Net Bags', 'category' => 'Supplies', 'unit' => 'pcs', 'quantity' => 30, 'reorder_level' => 10, 'unit_cost' => 45],
        ];

        foreach ($items as $item) {
            InventoryItem::updateOrCreate(['name' => $item['name']], $item);
        }

        if (Customer::count() === 0) {
            Customer::insert([
                ['first_name' => 'Maria', 'last_name' => 'Santos', 'phone' => '0917 123 4567', 'email' => 'maria@example.com', 'address' => '123 Sampaguita St., Brgy. San Roque', 'created_at' => now(), 'updated_at' => now()],
                ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'phone' => '0918 765 4321', 'email' => 'juan@example.com', 'address' => '45 Mango Ave., Brgy. Malinis', 'created_at' => now(), 'updated_at' => now()],
                ['first_name' => 'Ana', 'last_name' => 'Reyes', 'phone' => '0920 555 0101', 'email' => null, 'address' => '78 Narra St., Brgy. Mabini', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
