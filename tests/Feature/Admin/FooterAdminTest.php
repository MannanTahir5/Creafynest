<?php

namespace Tests\Feature\Admin;

use App\Models\FooterSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_footer_editor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/footer')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Footer/Edit')
                ->has('footer')
                ->has('tabs'));
    }

    public function test_admin_can_update_footer_links(): void
    {
        $user = User::factory()->create();
        FooterSetting::query()->create([
            ...FooterSetting::defaultScalars('Azee'),
            'payload' => FooterSetting::defaultPayload(),
        ]);

        $payload = FooterSetting::payloadForEditForm(FooterSetting::defaultPayload());
        $payload['company_links'][0]['label'] = 'About us';

        $this->actingAs($user)
            ->put('/admin/footer', [
                'active_tab' => 'column1',
                'logo_alt' => 'Azee',
                'home_aria_label' => 'Azee home',
                'resources_heading' => 'Resources',
                'contact_heading' => 'Contact us',
                'studio_label' => 'Studio',
                'studio_text' => 'Remote-first delivery.',
                'contact_email' => 'hello@example.com',
                'connect_heading' => 'Connect with us',
                'copyright_entity' => 'Azee',
                'organization_description' => 'We build web products.',
                'company_links' => $payload['company_links'],
                'resource_links' => $payload['resource_links'],
                'columns' => $payload['columns'],
                'socials' => $payload['socials'],
                'legal_links' => $payload['legal_links'],
            ])
            ->assertRedirect(route('admin.footer.edit', ['tab' => 'column1']));

        $row = FooterSetting::query()->first();
        $this->assertSame('About us', $row->payload['company_links'][0]['label']);
    }

    public function test_public_pages_receive_footer_props(): void
    {
        FooterSetting::query()->create([
            ...FooterSetting::defaultScalars('TestCo'),
            'payload' => FooterSetting::defaultPayload(),
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('footer')
                ->where('footer.copyright_entity', 'TestCo'));
    }
}
