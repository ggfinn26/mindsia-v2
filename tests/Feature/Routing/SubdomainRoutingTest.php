<?php

namespace Tests\Feature\Routing;

use Tests\TestCase;

/**
 * Routing subdomain: company/career dilayani di subdomain, path lama 301 redirect.
 */
class SubdomainRoutingTest extends TestCase
{
    public function test_company_landing_generated_on_company_subdomain(): void
    {
        $this->assertStringContainsString('company.mindsia.id', route('company.landing'));
    }

    public function test_career_routes_generated_on_career_subdomain(): void
    {
        $this->assertStringContainsString('career.mindsia.id', route('careers.index'));
        $this->assertStringContainsString('career.mindsia.id', route('careers.show', 1));
    }

    public function test_legacy_company_path_redirects_301_to_subdomain(): void
    {
        $this->get('http://mindsia.id/company')
            ->assertStatus(301)
            ->assertRedirect(route('company.landing'));
    }

    public function test_legacy_karir_paths_redirect_301_to_subdomain(): void
    {
        $this->get('http://mindsia.id/karir')
            ->assertStatus(301)
            ->assertRedirect(route('careers.index'));

        $this->get('http://mindsia.id/karir/7')
            ->assertStatus(301)
            ->assertRedirect(route('careers.show', 7));
    }
}
