<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscriber;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase10Test extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_subscribe_stores_email(): void
    {
        $this->from('/')
            ->post('/newsletter', ['email' => 'reader@example.com'])
            ->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
        ]);
        $this->assertNotNull(NewsletterSubscriber::query()->where('email', 'reader@example.com')->value('confirmed_at'));
    }

    public function test_newsletter_subscribe_validates_email(): void
    {
        $this->from('/')
            ->post('/newsletter', ['email' => 'not-an-email'])
            ->assertRedirect('/')
            ->assertSessionHasErrors(['email']);
    }

    public function test_portfolio_legacy_category_query_maps_to_categories_filter(): void
    {
        Project::factory()->create(['category' => 'Web']);
        Project::factory()->create(['category' => 'UI']);

        $page = $this->get('/portfolio?category=Web')->inertiaPage();

        $this->assertSame('Portfolio/Index', $page['component']);
        $this->assertSame(['Web'], $page['props']['filters']['categories']);
        $this->assertCount(1, $page['props']['projects']['data']);
    }

    public function test_portfolio_multi_category_comma_query(): void
    {
        Project::factory()->create(['category' => 'Web']);
        Project::factory()->create(['category' => 'Landing Page']);
        Project::factory()->create(['category' => 'UI']);

        $page = $this->get('/portfolio?categories=Web,Landing%20Page')->inertiaPage();

        $this->assertEqualsCanonicalizing(['Web', 'Landing Page'], $page['props']['filters']['categories']);
        $this->assertCount(2, $page['props']['projects']['data']);
    }

    public function test_portfolio_falls_back_to_public_case_study_art_when_project_has_no_storage_image(): void
    {
        if (! is_file(public_path('images/case-studies/myntist.jpg'))) {
            $this->markTestSkipped('Missing public/images/case-studies/myntist.jpg');
        }

        Project::query()->create([
            'title' => 'Myntist',
            'slug' => 'myntist',
            'category' => 'Web',
            'description' => 'Seeded case study.',
            'tech_stack' => ['Laravel'],
            'image' => null,
            'gallery' => null,
            'live_url' => null,
            'github_url' => null,
        ]);

        $page = $this->get('/portfolio')->inertiaPage();
        $row = collect($page['props']['projects']['data'])->firstWhere('slug', 'myntist');

        $this->assertNotNull($row['image_url'] ?? null);
        $this->assertSame('/images/case-studies/myntist.jpg', $row['image_url']);
    }

    public function test_case_study_page_exposes_editorial_sections_and_gallery(): void
    {
        Project::query()->create([
            'title' => 'Prior-auth review',
            'slug' => 'prior-auth-review',
            'category' => 'Healthcare',
            'description' => 'Problem details.',
            'tech_stack' => ['Python', 'FastAPI'],
            'image' => null,
            'gallery' => ['projects/gallery/a.jpg', 'projects/gallery/b.jpg'],
            'live_url' => 'https://example.com',
            'github_url' => 'https://github.com/example/project',
            'problem_title' => 'The problem.',
            'problem_text' => 'The backlog was growing.',
            'approach_title' => 'The approach.',
            'approach_text' => 'We started with a one-week discovery sprint.',
            'system_title' => 'The shape of the system.',
            'system_text' => 'Documents flow in from clinics through three channels.',
            'repeat_title' => "What we'd do again.",
            'repeat_text' => 'We kept the human-in-the-loop interface intentional.',
            'next_title' => "What's next.",
            'next_text' => 'The system is in steady-state operation.',
        ]);

        $page = $this->get('/portfolio/prior-auth-review')->inertiaPage();

        $this->assertSame('The problem.', $page['props']['project']['problem_title']);
        $this->assertSame('The backlog was growing.', $page['props']['project']['problem_text']);
        $this->assertSame('The approach.', $page['props']['project']['approach_title']);
        $this->assertSame('The shape of the system.', $page['props']['project']['system_title']);
        $this->assertCount(2, $page['props']['project']['gallery']);
    }

    public function test_case_study_page_exposes_named_story_sections_and_results_heading(): void
    {
        Project::query()->create([
            'title' => 'Ops clarity dashboard',
            'slug' => 'ops-clarity-dashboard',
            'category' => 'Operations',
            'description' => 'A workflow redesign project.',
            'tech_stack' => ['Laravel', 'Vue'],
            'image' => null,
            'gallery' => ['projects/gallery/a.jpg', 'projects/gallery/b.jpg'],
            'live_url' => 'https://example.com',
            'github_url' => 'https://github.com/example/project',
            'how_it_started_title' => 'How it started',
            'how_it_started_text' => 'The team was manually triaging requests every afternoon.',
            'challenge_title' => 'The challenge',
            'challenge_text' => 'Backlog visibility was inconsistent across operations.',
            'approach_title' => 'Our approach',
            'approach_text' => 'We mapped triage flow and replaced it with a focused workflow.',
            'results_title' => 'The results',
            'results_text' => 'Manual work dropped by 42% in the first month.',
        ]);

        $page = $this->get('/portfolio/ops-clarity-dashboard')->inertiaPage();

        $this->assertSame('How it started', $page['props']['project']['how_it_started_title']);
        $this->assertSame('The challenge', $page['props']['project']['challenge_title']);
        $this->assertSame('Our approach', $page['props']['project']['approach_title']);
        $this->assertSame('The results', $page['props']['project']['results_title']);
    }

    public function test_admin_newsletter_index_requires_auth(): void
    {
        $this->get('/admin/newsletter')->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_view_newsletter_subscribers(): void
    {
        NewsletterSubscriber::query()->create([
            'email' => 'sub@example.com',
            'confirmed_at' => now(),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/newsletter')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Admin/Newsletter/Index')
                ->has('subscribers.data', 1));
    }
}
