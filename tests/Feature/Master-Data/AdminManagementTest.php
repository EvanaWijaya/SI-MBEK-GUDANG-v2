<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = Admin::factory()->create([
            'role' => 'super_admin'
        ]);

        $this->actingAs(
            $this->superAdmin,
            'admin'
        );
    }

    /** @test */
    public function admin_dapat_dibuat()
    {
        $response = $this->post(
            route('admin.admins.store'),
            [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]
        );

        $response->assertRedirect(
            route('admin.admins.index')
        );

        $this->assertDatabaseHas(
            'admins',
            [
                'name' => 'Admin Baru',
                'email' => 'adminbaru@test.com',
                'role' => 'admin',
                'must_change_password' => 1,
            ]
        );
    }

    /** @test */
    public function email_admin_harus_unik()
    {
        Admin::factory()->create([
            'email' => 'admin@test.com'
        ]);

        $response = $this->post(
            route('admin.admins.store'),
            [
                'name' => 'Admin Baru',
                'email' => 'admin@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]
        );

        $response->assertSessionHasErrors(
            'email'
        );
    }

    /** @test */
    public function admin_dapat_diperbarui()
    {
        $admin = Admin::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->put(
            route('admin.admins.update', $admin->id),
            [
                'name' => 'Admin Update',
                'email' => 'update@test.com',
                'phone' => '08123456789',
            ]
        );

        $response->assertRedirect(
            route('admin.admins.index')
        );

        $this->assertDatabaseHas(
            'admins',
            [
                'id' => $admin->id,
                'name' => 'Admin Update',
                'email' => 'update@test.com',
                'phone' => '08123456789',
            ]
        );
    }

    /** @test */
    public function admin_dapat_dihapus()
    {
        $admin = Admin::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->delete(
            route('admin.admins.destroy', $admin->id)
        );

        $response->assertRedirect(
            route('admin.admins.index')
        );

        $this->assertDatabaseMissing(
            'admins',
            [
                'id' => $admin->id,
            ]
        );
    }

    /** @test */
    public function admin_tidak_bisa_menghapus_dirinya_sendiri()
    {
        $response = $this->delete(
            route(
                'admin.admins.destroy',
                $this->superAdmin->id
            )
        );

        $response->assertStatus(404);
    }
}