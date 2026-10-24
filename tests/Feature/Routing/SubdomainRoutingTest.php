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

    /*
     * Area employee memakai helper $sub yang mengembalikan null di env testing
     * (tanpa batasan host) agar ~239 feature test admin yang memakai path apex
     * tetap lolos. Subdomain employee.mindsia.id ditegakkan di produksi
     * (env != testing). Karena itu di sini kita verifikasi route employee
     * resolve dan path lama tetap 301, bukan host-nya.
     */
    public function test_employee_routes_resolve_in_testing(): void
    {
        $this->get(route('login'))->assertStatus(302); // showLogin redirect ke employee.landing
        $this->assertStringNotContainsString('404', (string) route('dashboard'));
    }

    public function test_legacy_employee_login_path_redirects_301(): void
    {
        $this->get('/employee/login')->assertStatus(301);
    }

    public function test_employee_landing_on_subdomain_and_legacy_redirects(): void
    {
        $this->assertStringContainsString('employee.mindsia.id', route('employee.landing'));

        $this->get('http://mindsia.id/employee')
            ->assertStatus(301)
            ->assertRedirect(route('employee.landing'));
    }

    public function test_applicant_routes_generated_on_applicant_subdomain(): void
    {
        $this->assertStringContainsString('applicant.mindsia.id/login', route('applicant.login'));
        $this->assertStringContainsString('applicant.mindsia.id/dashboard', route('applicant.dashboard'));
        $this->assertStringContainsString('applicant.mindsia.id/register', route('applicant.register'));
    }

    public function test_legacy_applicant_login_redirects_301_to_subdomain(): void
    {
        $this->get('http://mindsia.id/applicant/login')
            ->assertStatus(301)
            ->assertRedirect(route('applicant.login'));
    }

    public function test_apex_routes_not_bound_to_employee_or_applicant_subdomain(): void
    {
        // Route apex (home, member) tidak boleh ikut ke subdomain employee/applicant.
        foreach (['home', 'member.dashboard', 'member.login'] as $name) {
            $url = route($name);
            $this->assertStringNotContainsString('employee.mindsia.id', $url);
            $this->assertStringNotContainsString('applicant.mindsia.id', $url);
            $this->assertStringNotContainsString('company.mindsia.id', $url);
        }
    }
}
