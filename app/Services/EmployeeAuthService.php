<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAuthService
{
    public function __construct(private readonly TelegramLogService $telegramLog) {}

    public function verifyEmployeeCode(string $employeeCode): ?Employee
    {
        $employee = Employee::where('employee_code', $employeeCode)->first();

        if (! $employee) {
            $reason = 'kode tidak ditemukan';
        } elseif (! $employee->is_active) {
            $reason = 'karyawan tidak aktif';
            $employee = null;
        } elseif ($employee->user) {
            $reason = 'akun sudah terdaftar';
            $employee = null;
        } else {
            return $employee;
        }

        Log::warning('Employee code verification failed', [
            'employee_code' => $employeeCode,
            'reason' => $reason,
            'ip' => request()->ip(),
        ]);

        $this->telegramLog->log(
            'warning',
            'auth',
            'register_verify_failed',
            "Gagal verifikasi kode karyawan ({$reason}): {$employeeCode} | IP: ".request()->ip()
        );

        return null;
    }

    public function createAccount(Employee $employee, array $data): User
    {
        $employee->load('currentStatus.position');

        $user = DB::transaction(function () use ($employee, $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'employee_id' => $employee->id,
                'is_active' => true,
            ]);

            $employee->update(['email' => $data['email']]);

            $role = $employee->currentStatus?->position?->role;
            if ($role) {
                $user->assignRole($role);
            }

            return $user;
        });

        $user->sendEmailVerificationNotification();

        return $user;
    }

    public function syncRoleFromCurrentStatus(Employee $employee): void
    {
        $user = $employee->user;
        if (! $user) {
            return;
        }

        $employee->load('currentStatus.position.role');
        $role = $employee->currentStatus?->position?->role;

        if ($role) {
            $user->syncRoles([$role]);
        }
    }
}
