<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        // Default: Owner pesan
        $pemesan = Owner::factory()->create();

        // Default: yang mencatat juga Owner
        $pencatat = $pemesan;

        return [
            'po_code' => 'PO-' . now()->format('Ymd') . '-' .
                $this->faker->unique()->numberBetween(1000, 9999),

            'supplier_id' => Supplier::factory(),

            'type' => 'material',

            'order_date' => $this->faker->date(),

            'status' => 'draft',

            // ✅ Morph dipesan_oleh
            'ordered_by_id' => $pemesan->id,
            'ordered_by_type' => get_class($pemesan),

            // ✅ Morph dicatat_oleh
            'recorded_by_id' => $pencatat->id,
            'recorded_by_type' => get_class($pencatat),

            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * PO dipesan oleh Admin
     */
    public function dipesanOlehAdmin(): self
    {
        return $this->state(function () {
            $admin = Admin::factory()->create();

            return [
                'ordered_by_id' => $admin->id,
                'ordered_by_type' => get_class($admin),
                'recorded_by_id' => $admin->id,
                'recorded_by_type' => get_class($admin),
            ];
        });
    }

    /**
     * Admin mencatat untuk Owner
     */
    public function adminCatatUntukOwner(): self
    {
        return $this->state(function () {
            $owner = Owner::factory()->create();
            $admin = Admin::factory()->create();

            return [
                'ordered_by_id' => $owner->id,
                'ordered_by_type' => get_class($owner),
                'recorded_by_id' => $admin->id,
                'recorded_by_type' => get_class($admin),
            ];
        });
    }

    /**
     * Set status tertentu
     */
    public function status(string $status): self
    {
        return $this->state(fn() => [
            'status' => $status,
        ]);
    }
}
