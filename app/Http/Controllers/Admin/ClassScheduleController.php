<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\UpdateClassScheduleMaterialRequest;
use App\Models\ClassSchedule;
use Illuminate\Http\RedirectResponse;

class ClassScheduleController extends Controller
{
    public function update(UpdateClassScheduleMaterialRequest $request, ClassSchedule $schedule): RedirectResponse
    {
        abort_if(
            $schedule->schedule_date > today(),
            422,
            'Materi tidak bisa diisi sebelum tanggal sesi.'
        );

        $schedule->update(['material_taught' => $request->validated('material_taught')]);

        return back()->with('success', 'Materi sesi berhasil disimpan.');
    }
}
