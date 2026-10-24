<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\PositionAttendanceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PositionAttendanceSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.schedule_rule.manage'),
        ];
    }

    public function index(): View
    {
        $positions = Position::with('attendanceSetting')
            ->orderBy('position_name')
            ->get();

        return view('attendance.position-settings.index', compact('positions'));
    }

    public function edit(Position $position): View
    {
        $setting = PositionAttendanceSetting::firstOrNew(['position_id' => $position->id]);

        return view('attendance.position-settings.edit', compact('position', 'setting'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $data = $request->validate([
            'is_required' => ['required', 'boolean'],
            'is_session_required' => ['required', 'boolean'],
            'attendance_scope' => ['required', 'in:branch,area,anywhere'],
            'early_checkin_minutes' => ['nullable', 'integer', 'min:0', 'max:480'],
            'early_checkout_minutes' => ['nullable', 'integer', 'min:0', 'max:480'],
        ]);

        PositionAttendanceSetting::updateOrCreate(
            ['position_id' => $position->id],
            $data,
        );

        return redirect()->route('attendance.position-settings.index')
            ->with('success', "Pengaturan absensi untuk posisi {$position->position_name} disimpan.");
    }
}
