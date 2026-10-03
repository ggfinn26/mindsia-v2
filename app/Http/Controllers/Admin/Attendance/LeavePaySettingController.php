<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\UpdateLeavePaySettingRequest;
use App\Models\LeavePaySetting;
use App\Models\Position;
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
        $positions = Position::orderBy('role_id')->orderBy('hierarchy_order')->get();

        $settings = LeavePaySetting::all()->keyBy(fn ($s) => $s->position_id.'_'.$s->leave_type);

        return view('attendance.leave-pay-settings.index', compact('positions', 'settings'));
    }

    public function upsert(UpdateLeavePaySettingRequest $request): RedirectResponse
    {
        $data = $request->validated();

        LeavePaySetting::updateOrCreate(
            ['position_id' => $data['position_id'], 'leave_type' => $data['leave_type']],
            ['is_paid' => $data['is_paid']],
        );

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
