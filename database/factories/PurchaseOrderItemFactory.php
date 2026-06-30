<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(5, 50);
        $unit_price = $this->faker->numberBetween(1000, 10000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),

            // ❗ Default tidak set relasi apapun
            'material_id' => null,
            'product_id' => null,

            'quantity' => $quantity,
            'received_quantity' => null,
            'difference' => 0,

            'unit_price' => $unit_price,
            'subtotal' => $quantity * $unit_price,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | STATES
    |--------------------------------------------------------------------------
    */

    public function forMaterial(?Material $material = null): static
    {
        return $this->state(fn() => [
            'material_id' => $material?->id ?? Material::factory(),
            'product_id' => null,
        ]);
    }

    public function forProduct(?Product $product = null): static
    {
        return $this->state(fn() => [
            'material_id' => null,
            'product_id' => $product?->id ?? Product::factory(),
        ]);
    }

    public function received(?int $quantity = null): static
    {
        return $this->state(function (array $attributes) use ($quantity) {
            $quantity = $attributes['quantity'];
            $reveived = $quantity ?? $quantity;

            return [
                'received_quantity' => $reveived,
                'selisih' => $reveived - $quantity,
            ];
        });
    }
}