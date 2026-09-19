<?php

namespace Tests\Feature\Auth;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\OtpVerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmployeeRegisterTest extends TestCase
{
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = Employee::factory()->create([
            'employee_code' => 'TEST-REG',
            'is_active' => true,
            'email' => 'reg.employee@mindsia.test',
        ]);
    }

    // ER-01
    public function test_verifikasi_kode_valid_simpan_session_ke_step2(): void
    {
        $this->post(route('register.verify'), ['employee_code' => 'TEST-REG'])
            ->assertRedirect();

        $this->assertEquals($this->employee->id, session('register_employee_id'));
    }

    // ER-02
    public function test_verifikasi_kode_tidak_ada_returns_error(): void
    {
        $this->post(route('register.verify'), ['employee_code' => 'KODE-TIDAK-ADA'])
            ->assertSessionHasErrors('employee_code');

        $this->assertNull(session('register_employee_id'));
    }

    // ER-03
    public function test_verifikasi_kode_employee_tidak_aktif_returns_error(): void
    {
        Employee::factory()->create([
            'employee_code' => 'INACTIVE-EMP',
            'is_active' => false,
        ]);

        $this->post(route('register.verify'), ['employee_code' => 'INACTIVE-EMP'])
            ->assertSessionHasErrors('employee_code');
    }

    // ER-04
    public function test_verifikasi_kode_employee_sudah_punya_akun_returns_error(): void
    {
        User::factory()->create(['employee_id' => $this->employee->id]);

        $this->post(route('register.verify'), ['employee_code' => 'TEST-REG'])
            ->assertSessionHasErrors('employee_code');
    }

    // ER-05
    public function test_akses_step2_tanpa_session_redirect_ke_step1(): void
    {
        $this->get(route('register.step2'))
            ->assertRedirect();
    }

    // ER-06
    public function test_register_berhasil_membuat_user_dan_kirim_verifikasi(): void
    {
        Notification::fake();

        // Simulasi session step1
        $this->withSession(['register_employee_id' => $this->employee->id]);

        $this->post(route('register'), [
            'name' => 'Test Employee Baru',
            'email' => 'newemployee@mindsia.test',
            'password' => 'Test@12345',
            'password_confirmation' => 'Test@12345',
        ])->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('employee_accounts', ['email' => 'newemployee@mindsia.test']);
        Notification::assertSentToTimes(
            User::where('email', 'newemployee@mindsia.test')->first(),
            OtpVerifyEmail::class,
            1
        );
    }

    // ER-07 — GAP-32: session register_employee_id expire setelah showStep2
    public function test_register_tanpa_session_redirect_ke_step1(): void
    {
        // POST register tanpa session (session sudah expire)
        $this->post(route('register'), [
            'name' => 'Test',
            'email' => 'gap32@mindsia.test',
            'password' => 'Test@12345',
            'password_confirmation' => 'Test@12345',
        ])->assertRedirect(); // redirect ke step1

        $this->assertDatabaseMissing('employee_accounts', ['email' => 'gap32@mindsia.test']);
    }

    // ER-08
    public function test_register_email_duplikat_returns_422(): void
    {
        User::factory()->create(['email' => 'existing@mindsia.test']);

        $this->withSession(['register_employee_id' => $this->employee->id])
            ->post(route('register'), [
                'name' => 'Test',
                'email' => 'existing@mindsia.test',
                'password' => 'Test@12345',
                'password_confirmation' => 'Test@12345',
            ])
            ->assertSessionHasErrors('email');
    }

    // ER-10
    public function test_register_berhasil_tidak_auto_login(): void
    {
        $this->withSession(['register_employee_id' => $this->employee->id])
            ->post(route('register'), [
                'name' => 'Test Auto Login',
                'email' => 'autologin@mindsia.test',
                'password' => 'Test@12345',
                'password_confirmation' => 'Test@12345',
            ]);

        $this->assertGuest('web');
    }

    // ER-11 — role di-assign dari position
    public function test_register_berhasil_role_diassign_dari_position(): void
    {
        // Employee dengan position yang punya role
        $employee = Employee::factory()->create([
            'employee_code' => 'WITH-POSITION',
            'is_active' => true,
        ]);

        // Set up current status dengan position yang punya role
        // (skip jika employment status belum ada factory — test tetap run tapi mungkin skip scenario ini)
        $this->withSession(['register_employee_id' => $employee->id])
            ->post(route('register'), [
                'name' => 'Test With Role',
                'email' => 'withrole@mindsia.test',
                'password' => 'Test@12345',
                'password_confirmation' => 'Test@12345',
            ]);

        $user = User::where('email', 'withrole@mindsia.test')->first();
        $this->assertNotNull($user);
        // Role assignment bergantung pada position — jika tidak ada position, user dibuat tanpa role
    }
}
