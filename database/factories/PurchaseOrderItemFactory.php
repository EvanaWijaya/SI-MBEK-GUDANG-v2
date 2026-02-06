<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'material_id' => Material::factory(),
            'jumlah' => $this->faker->numberBetween(5, 50),
            'jumlah_diterima' => null, // default null, nanti diisi saat receive
            'harga_satuan' => $this->faker->numberBetween(1000, 10000),
            'subtotal' => function (array $attributes) {
                return $attributes['jumlah'] * $attributes['harga_satuan'];
            },
        ];
    }
}
