<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase8QaTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_validation_errors_on_empty_submission(): void
    {
        $this->from('/contact')
            ->post('/contact', [])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_unknown_project_slug_returns_not_found(): void
    {
        $this->get('/portfolio/does-not-exist-slug-xyz')->assertNotFound();
    }

    public function test_unknown_blog_slug_returns_not_found(): void
    {
        $this->get('/blog/does-not-exist-slug-xyz')->assertNotFound();
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }
}
