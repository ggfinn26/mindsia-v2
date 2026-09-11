<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\MemberClass;
use App\Services\ClassRoomService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class MemberClassController extends Controller
{
    public function __construct(private ClassRoomService $service) {
        $this->middleware('board-of-directors');
    }

    public function store(Request $request, ClassRoom $classroom): RedirectResponse
    {
        $validated = $request->validate([
            'member_registration_id' => 'required|integer',
            'start_date' => 'required|date',
        ]);

        $this->service->enrollMember($classroom, $validated['member_registration_id'], $validated['start_date']);
        return back()->with('success', 'Member berhasil didaftarkan');
    }

    public function destroy(MemberClass $memberClass): RedirectResponse
    {
        $classId = $memberClass->class_id;
        $this->service->removeMember($memberClass);
        return redirect()->route('classrooms.show', $classId)->with('success', 'Member berhasil dihapus');
    }

    public function transfer(Request $request, MemberClass $memberClass): RedirectResponse
    {
        $validated = $request->validate(['new_class_id' => 'required|exists:classes,id']);
        $newClass = ClassRoom::findOrFail($validated['new_class_id']);
        $this->service->transferMember($memberClass, $newClass);
        return redirect()->route('classrooms.show', $newClass)->with('success', 'Member berhasil dipindahkan');
    }
}
