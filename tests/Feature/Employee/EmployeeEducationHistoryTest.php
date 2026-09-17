<?php

namespace Tests\Feature\Employee;

use App\Models\Employee;
use App\Models\EmployeeEducation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

// Flow: employee-education-history (EH-01 → EH-05)
// Covers: self-add, authorization gaps, self-edit, others-edit, level validation
class EmployeeEducationHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $employeeUser;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->employee = Employee::factory()->create();
        $this->employeeUser = User::factory()->create([
            'employee_id' => $this->employee->id,
            'email' => $this->employee->email,
        ]);
    }

    // EH-01: Add education history for self → succeeds
    public function test_add_education_for_self(): void
    {
        $this->actingAs($this->employeeUser)
            ->post(route('employees.educations.store', $this->employee), [
                'level' => 'S1',
                'institution' => 'Universitas Indonesia',
                'major' => 'Teknik Informatika',
                'graduation_year' => '2020',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_educations', [
            'employee_id' => $this->employee->id,
            'level' => 'S1',
            'institution' => 'Universitas Indonesia',
            'major' => 'Teknik Informatika',
            'graduation_year' => '2020',
        ]);
    }

    // EH-02: GAP-211 — store() uses `can('employee.update')` → any user with that
    // permission can add education for ANY employee (not just self)
    public function test_gap211_admin_can_add_education_for_any_employee(): void
    {
        $updatePerm = Permission::firstOrCreate(['name' => 'employee.update', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->givePermissionTo($updatePerm);

        // Admin adds education for an employee they don't own
        $this->actingAs($admin)
            ->post(route('employees.educations.store', $this->employee), [
                'level' => 'S2',
                'institution' => 'ITB',
                'major' => 'Computer Science',
                'graduation_year' => '2023',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_educations', [
            'employee_id' => $this->employee->id,
            'level' => 'S2',
        ]);
    }

    // EH-02b: User without employee.update cannot add education for others
    public function test_user_without_permission_cannot_add_education(): void
    {
        $otherUser = User::factory()->create();
        $otherEmployee = Employee::factory()->create();

        $this->actingAs($otherUser)
            ->post(route('employees.educations.store', $otherEmployee), [
                'level' => 'S1',
                'institution' => 'UGM',
            ])
            ->assertStatus(403);
    }

    // EH-03: Edit own education → succeeds (EmployeeEducationPolicy checks ownership)
    public function test_edit_own_education(): void
    {
        $education = EmployeeEducation::create([
            'employee_id' => $this->employee->id,
            'level' => 'S1',
            'institution' => 'Universitas Indonesia',
            'major' => 'Teknik Informatika',
            'graduation_year' => 2020,
        ]);

        $this->actingAs($this->employeeUser)
            ->patch(route('employees.educations.update', [$this->employee, $education]), [
                'level' => 'S1',
                'institution' => 'Universitas Gadjah Mada',
                'major' => 'Ilmu Komputer',
                'graduation_year' => '2020',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('employee_educations', [
            'id' => $education->id,
            'institution' => 'Universitas Gadjah Mada',
        ]);
    }

    // EH-04: Edit someone else's education → 403 (EmployeeEducationPolicy)
    public function test_edit_others_education_forbidden(): void
    {
        $otherEmployee = Employee::factory()->create();
        $otherUser = User::factory()->create([
            'employee_id' => $otherEmployee->id,
            'email' => $otherEmployee->email,
        ]);

        $education = EmployeeEducation::create([
            'employee_id' => $this->employee->id,
            'level' => 'S1',
            'institution' => 'Universitas Indonesia',
        ]);

        $this->actingAs($otherUser)
            ->patch(route('employees.educations.update', [$this->employee, $education]), [
                'level' => 'S1',
                'institution' => 'Hacked University',
            ])
            ->assertStatus(403);
    }

    // EH-05: Invalid level → 422 (in:SD,SMP,SMA,D3,S1,S2,S3)
    public function test_invalid_level_rejected(): void
    {
        $this->actingAs($this->employeeUser)
            ->post(route('employees.educations.store', $this->employee), [
                'level' => 'S4',
                'institution' => 'Fake University',
            ])
            ->assertSessionHasErrors('level');
    }

    // EH-05b: Valid levels accepted
    public function test_valid_levels_accepted(): void
    {
        $validLevels = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'];

        foreach ($validLevels as $level) {
            $this->actingAs($this->employeeUser)
                ->post(route('employees.educations.store', $this->employee), [
                    'level' => $level,
                    'institution' => "School $level",
                    'graduation_year' => '2020',
                ])
                ->assertRedirect();
        }

        $this->assertCount(count($validLevels), $this->employee->educations);
    }

    // EH-delete: Delete own education → succeeds
    public function test_delete_own_education(): void
    {
        $education = EmployeeEducation::create([
            'employee_id' => $this->employee->id,
            'level' => 'S1',
            'institution' => 'UI',
        ]);

        $this->actingAs($this->employeeUser)
            ->delete(route('employees.educations.destroy', [$this->employee, $education]))
            ->assertRedirect();

        $this->assertDatabaseMissing('employee_educations', ['id' => $education->id]);
    }

    // Auth: Unauthenticated → redirect login
    public function test_unauthenticated_redirect_to_login(): void
    {
        $this->post(route('employees.educations.store', $this->employee), [
            'level' => 'S1',
            'institution' => 'UI',
        ])
            ->assertRedirect(route('login'));
    }
}
