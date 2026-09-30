<?php

namespace Tests\Feature\Admin;

use App\Models\DeliveryCategory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        return User::factory()->create();
    }

    /** Minimal JSON payload that satisfies {@see \App\Http\Requests\Admin\ServiceFormRules}. */
    private function validServicePayload(array $overrides = []): array
    {
        $content = [
            'hero' => [
                'eyebrow' => '',
                'heading_prefix' => 'Test',
                'heading_highlight' => 'Highlight',
                'subhead' => '',
                'description' => 'Hero description text long enough.',
                'chips' => [],
                'primary_cta_label' => 'Contact',
                'primary_cta_href' => '/contact',
                'secondary_cta_label' => '',
                'secondary_cta_href' => '',
                'image_alt' => '',
                'caption_eyebrow' => '',
                'caption_text' => '',
            ],
            'tech' => [
                'eyebrow' => '',
                'heading_prefix' => 'Tech',
                'heading_highlight' => '',
                'description' => 'Tech section description here.',
                'items' => [
                    ['name' => 'Laravel', 'slug' => 'laravel'],
                ],
                'cta_label' => '',
                'cta_href' => '',
                'image_alt' => '',
            ],
            'outcomes' => [
                'eyebrow' => '',
                'heading' => 'Outcomes',
                'description' => 'What we deliver description.',
                'cards' => [
                    [
                        'icon' => 'LayoutTemplate',
                        'title' => 'Card title',
                        'description' => 'Card description text.',
                    ],
                ],
                'cta_label' => '',
                'cta_href' => '',
                'image_alt' => '',
            ],
            'snapshot' => [
                'eyebrow' => '',
                'heading_prefix' => '',
                'heading_highlight' => '',
                'heading_suffix' => '',
                'paragraphs' => [],
                'caption_title' => '',
                'caption_subtitle' => '',
                'image_alt' => '',
            ],
        ];

        return array_merge([
            'title' => 'CRUD Test Service',
            'slug' => 'crud-test-service',
            'icon' => 'Sparkles',
            'sort_order' => 1,
            'is_active' => true,
            'delivery_category_id' => null,
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'og_title' => '',
            'og_description' => '',
            'noindex' => false,
            'canonical_url' => null,
            'schema_type' => null,
            'content' => $content,
        ], $overrides);
    }

    public function test_guest_cannot_access_admin_services_index(): void
    {
        $this->get(route('admin.services.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_list_create_edit_update_delete_multiple_services(): void
    {
        $user = $this->actingAdmin();
        $category = DeliveryCategory::query()->create([
            'slug' => 'test-cat',
            'title' => 'Test category',
            'subtitle' => 'Sub',
            'feature_icon' => 'LayoutGrid',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($user)->get(route('admin.services.index'))->assertOk();

        $payloadA = $this->validServicePayload([
            'title' => 'Alpha Service',
            'slug' => 'alpha-service',
            'delivery_category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->post(route('admin.services.store'), $payloadA)
            ->assertRedirect(route('admin.services.index'));

        $serviceA = Service::query()->where('slug', 'alpha-service')->first();
        $this->assertNotNull($serviceA);
        $this->assertSame('Alpha Service', $serviceA->title);

        $payloadB = $this->validServicePayload([
            'title' => 'Beta Service',
            'slug' => 'beta-service',
        ]);

        $this->actingAs($user)
            ->post(route('admin.services.store'), $payloadB)
            ->assertRedirect(route('admin.services.index'));

        $this->assertSame(2, Service::query()->count());

        $serviceB = Service::query()->where('slug', 'beta-service')->first();
        $this->assertNotNull($serviceB);

        $this->actingAs($user)
            ->get(route('admin.services.edit', $serviceA))
            ->assertOk();

        $update = $this->validServicePayload([
            'title' => 'Alpha Updated',
            'slug' => 'alpha-service',
            'delivery_category_id' => $category->id,
        ]);
        $update['content']['hero']['heading_prefix'] = 'Updated';

        $this->actingAs($user)
            ->put(route('admin.services.update', $serviceA), $update)
            ->assertRedirect(route('admin.services.index'));

        $serviceA->refresh();
        $this->assertSame('Alpha Updated', $serviceA->title);
        $this->assertSame('Updated', $serviceA->content['hero']['heading_prefix']);

        $this->actingAs($user)
            ->delete(route('admin.services.destroy', $serviceB))
            ->assertRedirect(route('admin.services.index'));

        $this->assertNull(Service::query()->where('slug', 'beta-service')->first());
        $this->assertSame(1, Service::query()->count());
    }

    public function test_public_can_view_active_service_by_slug_and_not_inactive(): void
    {
        $active = Service::query()->create([
            'delivery_category_id' => null,
            'title' => 'Public Active',
            'slug' => 'public-active',
            'description' => 'Desc',
            'icon' => 'Sparkles',
            'sort_order' => 0,
            'is_active' => true,
            'content' => $this->validServicePayload()['content'],
        ]);

        $this->get('/services/public-active')->assertOk();

        $active->update(['is_active' => false]);

        $this->get('/services/public-active')->assertNotFound();
    }
}
