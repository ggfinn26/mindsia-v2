<?php

namespace Tests\Feature\Attendance;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeAttendanceMonthlyRecap;
use App\Models\EmployeeWorkAttendanceLog;
use App\Models\EmploymentStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyAttendanceRecapTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Employee $employee;

    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create([
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius_meters' => 500,
        ]);

        $this->employee = Employee::factory()->create([
            'branch_id' => $this->branch->id,
        ]);

        $this->user = User::factory()->create([
            'employee_id' => $this->employee->id,
        ]);
    }

    private function createWorkLog(Employee $employee, string $date, string $status, int $lateMinutes = 0, int $earlyLeaveMinutes = 0): EmployeeWorkAttendanceLog
    {
        $data = [
            'employee_id' => $employee->id,
            'branch_id' => $this->branch->id,
            'attendance_date' => $date,
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'is_location_anomaly' => false,
        ];

        if (in_array($status, ['checked_in', 'present', 'late'])) {
            $data['check_in'] = "{$date} 08:00:00";
            $data['check_in_latitude'] = -6.2088;
            $data['check_in_longitude'] = 106.8456;
        }

        if (in_array($status, ['present'])) {
            $data['check_out'] = "{$date} 17:00:00";
            $data['check_out_latitude'] = -6.2088;
            $data['check_out_longitude'] = 106.8456;
        }

        return EmployeeWorkAttendanceLog::create($data);
    }

    // ── MR-01: Recap diupdate setelah check-out ──
    // When a work attendance log is updated (e.g. check-out), the observer fires upsertForEmployee.

    public function test_mr01_recap_updated_after_checkout(): void
    {
        $date = now()->toDateString();

        // Create log (fires observer `created`)
        $log = $this->createWorkLog($this->employee, $date, 'checked_in');

        // Update to present (fires observer `updated`)
        $log->update([
            'status' => 'present',
            'check_out' => now()->setTime(17, 0),
            'check_out_latitude' => -6.2088,
            'check_out_longitude' => 106.8456,
        ]);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap, 'Recap should be created by observer');
        $this->assertGreaterThanOrEqual(1, $recap->total_present);
    }

    // ── MR-02: Recap diupdate setelah adjustment ──
    // Adjusting a log triggers `updated` observer → recap refreshed.

    public function test_mr02_recap_updated_after_adjustment(): void
    {
        $date = now()->toDateString();

        $log = $this->createWorkLog($this->employee, $date, 'absent');

        // Adjust: change status from absent → present
        $log->update([
            'status' => 'present',
            'check_in' => "{$date} 08:00:00",
            'check_out' => "{$date} 17:00:00",
        ]);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap);
        $this->assertGreaterThanOrEqual(1, $recap->total_present);
        $this->assertEquals(0, $recap->total_absent);
    }

    // ── MR-03: Recap diupdate setelah leave approved ──
    // Leave approval updates attendance logs → fires `updated` observer.

    public function test_mr03_recap_updated_after_leave_approved(): void
    {
        $date = now()->toDateString();

        // Create a log, then change status to leave (simulating leave approval effect)
        $log = $this->createWorkLog($this->employee, $date, 'present');

        $log->update(['status' => 'leave']);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap);
        $this->assertEquals(0, $recap->total_present);
        $this->assertGreaterThanOrEqual(1, $recap->total_leave);
    }

    // ── MR-04: Recap tidak diupdate saat check-in (GAP-82 discrepancy) ──
    // SKENARIO says observer only handles `updated`, but actual code handles BOTH `created` + `updated`.

    public function test_mr04_recap_updated_on_created_observer(): void
    {
        // Create a new work attendance log → fires `created` observer
        $this->createWorkLog($this->employee, now()->toDateString(), 'present');

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        // Actual behavior: observer DOES handle `created` — SKENARIO-TESTING says it doesn't (GAP-82 discrepancy)
        $this->assertNotNull($recap, 'Observer handles created event — SKENARIO GAP-82 says it does not, but actual code does');
    }

    // ── MR-05: total_present dihitung benar ──

    public function test_mr05_total_present_calculated_correctly(): void
    {
        $year = now()->year;
        $month = now()->month;
        $date = now()->toDateString();

        // Create 3 present logs
        for ($i = 0; $i < 3; $i++) {
            $this->createWorkLog($this->employee, $date, 'present');
        }

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        $this->assertNotNull($recap);
        $this->assertEquals(3, $recap->total_present);
    }

    // ── MR-06: total_late dihitung benar ──

    public function test_mr06_total_late_calculated_correctly(): void
    {
        $date = now()->toDateString();

        // Create 2 logs with late_minutes > 0
        // Note: work attendance ENUM has 'late' status (valid for work, unlike session)
        $this->createWorkLog($this->employee, $date, 'late', lateMinutes: 30);
        $this->createWorkLog($this->employee, $date, 'late', lateMinutes: 15);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap);
        $this->assertEquals(2, $recap->total_late);
        $this->assertEquals(45, $recap->total_late_minutes);
    }

    // ── MR-07: total_scheduled_working_days ──
    // SKENARIO says "BUG: selalu 0" (GAP-80), but code calculates weekday count correctly.

    public function test_mr07_total_scheduled_working_days_calculated(): void
    {
        $date = now()->toDateString();

        $this->createWorkLog($this->employee, $date, 'present');

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap);

        // Verify it's a weekday count (not 0 and not total days)
        $expectedWeekdays = collect(range(1, now()->daysInMonth))
            ->filter(fn ($day) => \Carbon\Carbon::create(now()->year, now()->month, $day)->isWeekday())
            ->count();

        $this->assertEquals($expectedWeekdays, $recap->total_scheduled_working_days,
            'total_scheduled_working_days should be weekday count for the month');
    }

    // ── MR-08: status=present_late tidak di-aggregate (GAP-83) ──
    // Work attendance ENUM doesn't have 'present_late' — SQL SUM(status='present_late') always returns 0.

    public function test_mr08_present_late_not_aggregated(): void
    {
        $date = now()->toDateString();

        // Create a present log with late_minutes (the way late attendance works in work schedule)
        $this->createWorkLog($this->employee, $date, 'present', lateMinutes: 20);

        $recap = EmployeeAttendanceMonthlyRecap::where('employee_id', $this->employee->id)
            ->where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->first();

        $this->assertNotNull($recap);
        // present_late doesn't exist in ENUM → SQL SUM(status='present_late') = 0
        // But late attendance is tracked via total_late (late_minutes > 0)
        $this->assertGreaterThanOrEqual(1, $recap->total_present);
        $this->assertGreaterThanOrEqual(1, $recap->total_late);
    }
}
