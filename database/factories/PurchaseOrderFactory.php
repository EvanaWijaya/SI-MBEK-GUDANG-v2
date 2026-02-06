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
        // Default: dipesan oleh Owner, dicatat oleh Owner juga
        $pemesan = Owner::factory()->create();
        $pencatat = $pemesan; // Yang pesan = yang catat (default)

        return [
            'kode_po' => 'PO-' . now()->format('Ymd') . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'supplier_id' => Supplier::factory(),
            'tanggal_pesan' => $this->faker->date(),
            'status' => $this->faker->randomElement([
                'draft',
                'dipesan',
                'diterima',
                'dibatalkan'
            ]),
            
            // Yang pesan
            'dipesan_oleh_id' => $pemesan->id,
            'dipesan_oleh_type' => get_class($pemesan),
            
            // ✅ PERBAIKAN: Yang catat (polymorphic)
            'dicatat_oleh_id' => $pencatat->id,
            'dicatat_oleh_type' => get_class($pencatat),
            
            'catatan_owner' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * PO dipesan oleh Admin, dicatat oleh Admin
     */
    public function dipesanOlehAdmin(): self
    {
        return $this->state(function () {
            $admin = Admin::factory()->create();

            return [
                'dipesan_oleh_id' => $admin->id,
                'dipesan_oleh_type' => get_class($admin),
                'dicatat_oleh_id' => $admin->id,
                'dicatat_oleh_type' => get_class($admin),
            ];
        });
    }

    /**
     * Admin catat PO untuk Owner
     */
    public function adminCatatUntukOwner(): self
    {
        return $this->state(function () {
            $owner = Owner::factory()->create();
            $admin = Admin::factory()->create();

            return [
                'dipesan_oleh_id' => $owner->id,
                'dipesan_oleh_type' => get_class($owner),
                'dicatat_oleh_id' => $admin->id,  // Yang catat = Admin
                'dicatat_oleh_type' => get_class($admin),
            ];
        });
    }

    /**
     * PO dengan status tertentu
     */
    public function status(string $status): self
    {
        return $this->state(function () use ($status) {
            return [
                'status' => $status,
            ];
        });
    }
}