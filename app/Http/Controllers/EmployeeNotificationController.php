<?php

namespace App\Http\Controllers;

use App\Models\EmployeeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $employee = $request->user()->employee;

        EmployeeNotification::where('employee_id', $employee->id)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        $notifications = EmployeeNotification::where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('notification.employee.index', compact('notifications'));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = EmployeeNotification::where('employee_id', $request->user()->employee->id)
            ->where('status', 'unread')
            ->count();

        return response()->json(['count' => $count]);
    }
}
