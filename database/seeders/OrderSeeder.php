<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrFail();

        $product = Product::where(
            'product_name',
            'Pakan Starter'
        )->firstOrFail();

        Order::create([
            'user_id' => $user->id,

            'orderable_id' => $product->id,
            'orderable_type' => Product::class,

            'order_id' => 'ORD-0001',

            'gross_amount' => 170000,

            'name' => 'Test User',
            'address' => 'Bandar Lampung',
            'phone' => '081234567890',

            'qty' => 2,

            'status' => 'success',
            'payment_method' => 'manual',
        ]);
    }
}