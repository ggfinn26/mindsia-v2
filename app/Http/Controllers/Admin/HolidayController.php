<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreHolidayRequest;
use App\Http\Requests\Attendance\UpdateHolidayRequest;
use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class HolidayController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:attendance.holiday.manage'),
        ];
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('work-schedule-rules.index');
    }

    public function create(): View
    {
        return view('attendance.holidays.create');
    }

    public function store(StoreHolidayRequest $request): RedirectResponse
    {
        Holiday::create($request->validated());

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Hari libur berhasil ditambahkan');
    }

    public function edit(Holiday $holiday): View
    {
        return view('attendance.holidays.edit', [
            'holiday' => $holiday,
        ]);
    }

    public function update(UpdateHolidayRequest $request, Holiday $holiday): RedirectResponse
    {
        $holiday->update($request->validated());

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Hari libur berhasil diperbarui');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->route('work-schedule-rules.index')
            ->with('success', 'Hari libur berhasil dihapus');
    }
}
