<?php

namespace Tests\Feature;

use Tests\TestCase;

class Phase7EndpointsTest extends TestCase
{
    public function test_sitemap_returns_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $this->assertStringContainsString('<urlset', $response->getContent());
    }

    public function test_robots_txt_disallows_admin_and_points_to_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Sitemap:', $body);
    }
}
