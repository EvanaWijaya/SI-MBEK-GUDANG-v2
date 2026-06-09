<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_profile_routes()
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
        $this->patch(route('profile.update'))->assertRedirect(route('login'));
        $this->delete(route('profile.destroy'))->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_view_edit_profile_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertStatus(200)
            ->assertViewIs('profile.edit')
            ->assertViewHas('user', $user);
    }

    /** @test */
    public function user_can_update_profile_without_picture()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'alamat' => 'New Address',
            'no_telepon' => '1234567890',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /** @test */
public function user_can_update_profile_with_picture()
{
    Storage::fake('public');
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'With Picture',
        'email' => $user->email,
        'profile_picture' => $file,
        'alamat' => 'New Address',
        'no_telepon' => '1234567890',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'With Picture',
    ]);

    $this->assertNotNull($user->fresh()->profile_picture);
}


    /** @test */
    public function updating_email_resets_email_verified_at()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'newmail@example.com',
            'alamat' => 'New Address',
            'no_telepon' => '0987654321',
        ]);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function user_can_delete_account_with_correct_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_cannot_delete_account_with_wrong_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('password', null, 'userDeletion');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
