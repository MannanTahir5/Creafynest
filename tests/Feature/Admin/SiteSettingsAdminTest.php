<?php

namespace Tests\Feature\Admin;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_site_settings(): void
    {
        $this->get('/admin/site-settings')->assertRedirect();
    }

    public function test_admin_can_update_site_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/admin/site-settings', [
                'app_name' => 'Custom Site',
                'app_url' => 'https://example.org',
                'timezone' => 'Europe/Berlin',
                'locale' => 'de',
                'ga4_measurement_id' => 'G-TEST12345',
            ])
            ->assertRedirect();

        $row = SiteSetting::query()->first();
        $this->assertNotNull($row);
        $this->assertSame('Custom Site', $row->app_name);
        $this->assertSame('https://example.org', $row->app_url);
        $this->assertSame('Europe/Berlin', $row->timezone);
        $this->assertSame('de', $row->locale);
        $this->assertSame('G-TEST12345', $row->ga4_measurement_id);
    }

    public function test_admin_can_clear_optional_fields_to_null(): void
    {
        $user = User::factory()->create();
        SiteSetting::query()->create([
            'app_name' => 'X',
            'ga4_measurement_id' => 'G-OLD',
        ]);

        $this->actingAs($user)
            ->put('/admin/site-settings', [
                'app_name' => '',
                'app_url' => '',
                'timezone' => '',
                'locale' => '',
                'ga4_measurement_id' => '',
            ])
            ->assertRedirect();

        $row = SiteSetting::query()->first();
        $this->assertNotNull($row);
        $this->assertNull($row->app_name);
        $this->assertNull($row->ga4_measurement_id);
    }
}
