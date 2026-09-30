<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeploymentHealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->get('/up')->assertOk();
    }
}
