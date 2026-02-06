<?php

namespace Tests\Feature\PurchaseOrder;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\PurchaseOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApprovePurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_approve_purchase_order()
    {
        $admin = Admin::factory()->create([
            'must_change_password' => false, // tambahkan ini biar gak redirect ke change password
        ]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'status' => 'draft'
        ]);

        // Admin coba akses route owner (harusnya ditolak)
        $response = $this->actingAs($admin, 'admin')
            ->put(route('owner.purchase-orders.approve', $purchaseOrder)); // route sudah diubah jadi 'approve'

        $response->assertStatus(302);
    }

    public function test_owner_can_approve_purchase_order()
    {
        $owner = Owner::factory()->create([
            'must_change_password' => false, // tambahkan ini biar gak redirect ke change password
        ]);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'status' => 'draft'
        ]);

        $response = $this->actingAs($owner, 'owner')
            ->put(route('owner.purchase-orders.approve', $purchaseOrder)); // route sudah diubah jadi 'approve'

        $response->assertStatus(302);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'dipesan', // status tetap 'dipesan' karena approve = dipesan
        ]);
    }
}