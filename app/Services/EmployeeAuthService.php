<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class EmployeeAuthService
{
    public function verifyEmployeeCode(string $employeeCode): ?Employee
    {
        $employee = Employee::where('employee_code', $employeeCode)
            ->where('is_active', true)
            ->whereNull('user_id')
            ->first();

        if (!$employee) {
            return null;
        }

        return $employee;
    }

    public function createAccount(Employee $employee, array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'employee_id' => $employee->id,
        ]);

        $employee->update(['email' => $data['email']]);

        if ($employee->employment_status) {
            $role = $employee->employment_status->position->role ?? null;
            if ($role) {
                $user->assignRole($role);
            }
        }

        return $user;
    }

    private function notifyDevTelegram(string $context, array $details): void
    {
        // TODO: Implement Telegram notification to dev channel
    }
}
