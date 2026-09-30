<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_profile(): void
    {
        $this->get('/admin/profile')->assertRedirect();
    }

    public function test_admin_can_view_profile_page(): void
    {
        $user = User::factory()->create(['name' => 'Jane', 'email' => 'jane@example.com']);

        $this->actingAs($user)
            ->get('/admin/profile')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Profile/Edit')
                ->where('profile.name', 'Jane')
                ->where('profile.email', 'jane@example.com'));
    }

    public function test_admin_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old',
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->put('/admin/profile', [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_profile_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'mine@example.com']);

        $this->actingAs($user)
            ->put('/admin/profile', [
                'name' => 'Mine',
                'email' => 'taken@example.com',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPass123'),
        ]);

        $this->actingAs($user)
            ->put('/admin/profile/password', [
                'current_password' => 'OldPass123',
                'password' => 'NewPass456',
                'password_confirmation' => 'NewPass456',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewPass456', $user->password));
    }

    public function test_password_update_requires_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPass123'),
        ]);

        $this->actingAs($user)
            ->put('/admin/profile/password', [
                'current_password' => 'wrong',
                'password' => 'NewPass456',
                'password_confirmation' => 'NewPass456',
            ])
            ->assertSessionHasErrors('current_password');
    }
}
