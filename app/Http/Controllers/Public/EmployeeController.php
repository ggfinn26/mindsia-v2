<?php

namespace App\Http\Controllers\Public;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController
{
    public function byCode(Request $request): JsonResponse
    {
        $code = $request->query('code');
        if (! $code) {
            return response()->json([]);
        }

        $employee = Employee::where('employee_code', $code)->first(['id', 'full_name', 'employee_code']);

        return response()->json($employee ?? []);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $employees = Employee::where('full_name', 'like', "%{$q}%")
            ->orWhere('employee_code', 'like', "%{$q}%")
            ->orderBy('full_name')
            ->limit(15)
            ->get(['full_name', 'employee_code']);

        return response()->json($employees);
    }
}
