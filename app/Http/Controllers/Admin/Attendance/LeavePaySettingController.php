<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\UpdateLeavePaySettingRequest;
use App\Models\LeavePaySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class LeavePaySettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:leave.pay_settings.manage'),
        ];
    }

    public function index(): View
    {
        return view('attendance.leave-pay-settings.index', [
            'settings' => LeavePaySetting::all(),
        ]);
    }

    public function update(UpdateLeavePaySettingRequest $request, LeavePaySetting $leavePaySetting): RedirectResponse
    {
        $leavePaySetting->update($request->validated());

        return redirect()->route('leave-pay-settings.index')
            ->with('success', 'Pengaturan pembayaran izin berhasil diperbarui');
    }
}
