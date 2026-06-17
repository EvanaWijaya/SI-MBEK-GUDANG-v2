<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Admin;
use App\Models\Owner;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Owner::query()->firstOrFail();
        $admin = Admin::query()->firstOrFail();

        $supplier = Supplier::query()->firstOrFail();

        // PO Draft
        PurchaseOrder::create([
            'purchase_order_code' => 'PO-20250811-0001',
            'supplier_id' => $supplier->id,
            'type' => 'material',
            'order_date' => now()->subDays(7),
            'status' => 'draft',

            'ordered_by_id' => $owner->id,
            'ordered_by_type' => Owner::class,

            'recorded_by_id' => $owner->id,
            'recorded_by_type' => Owner::class,

            'notes' => 'PO pembelian bahan baku pakan.',
        ]);

        // PO Disetujui
        PurchaseOrder::create([
            'purchase_order_code' => 'PO-20250811-0002',
            'supplier_id' => $supplier->id,
            'type' => 'material',
            'order_date' => now()->subDays(5),
            'status' => 'ordered',

            'ordered_by_id' => $owner->id,
            'ordered_by_type' => Owner::class,

            'recorded_by_id' => $admin->id,
            'recorded_by_type' => Admin::class,

            'notes' => 'PO disetujui untuk produksi pakan starter.',
        ]);

        // PO Selesai
        PurchaseOrder::create([
            'purchase_order_code' => 'PO-20250811-0003',
            'supplier_id' => $supplier->id,
            'type' => 'material',
            'order_date' => now()->subDays(2),
            'status' => 'received',

            'ordered_by_id' => $owner->id,
            'ordered_by_type' => Owner::class,

            'recorded_by_id' => $admin->id,
            'recorded_by_type' => Admin::class,

            'notes' => 'Material telah diterima lengkap.',
        ]);
    }
}