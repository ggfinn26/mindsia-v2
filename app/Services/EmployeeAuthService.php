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
        $employee = Employee::where('employee_code', $employeeCode)
            ->where('is_active', true)
            ->whereDoesntHave('user')
            ->first();

        if (! $employee) {
            Log::warning('Employee code verification failed', [
                'employee_code' => $employeeCode,
                'ip' => request()->ip(),
            ]);

            $this->telegramLog->log(
                'warning',
                'auth',
                'register_verify_failed',
                "Kode karyawan tidak valid atau sudah punya akun: {$employeeCode} | IP: ".request()->ip()
            );

            return null;
        }

        return $employee;
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
