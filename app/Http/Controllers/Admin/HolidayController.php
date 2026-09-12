<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreHolidayRequest;
use App\Models\Holiday;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->middleware('board-of-directors');
    }

    public function index(): View
    {
        return view('attendance.holidays.index', [
            'holidays' => Holiday::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('attendance.holidays.create');
    }

    public function store(StoreHolidayRequest $request): RedirectResponse
    {
        Holiday::create($request->validated());

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil ditambahkan');
    }

    public function show(Holiday $holiday): View
    {
        return view('attendance.holidays.show', [
            'holiday' => $holiday,
        ]);
    }

    public function edit(Holiday $holiday): View
    {
        return view('attendance.holidays.edit', [
            'holiday' => $holiday,
        ]);
    }

    public function update(StoreHolidayRequest $request, Holiday $holiday): RedirectResponse
    {
        $holiday->update($request->validated());

        return redirect()->route('holidays.show', $holiday)
            ->with('success', 'Hari libur berhasil diperbarui');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->route('holidays.index')
            ->with('success', 'Hari libur berhasil dihapus');
    }
}
