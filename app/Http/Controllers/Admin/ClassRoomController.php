<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreClassRoomRequest;
use App\Http\Requests\ClassRoom\UpdateClassRoomRequest;
use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Program;
use App\Repositories\ClassRoomRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassRoomController extends Controller
{
    public function __construct(private ClassRoomRepository $repository)
    {
        $this->middleware('board-of-directors');
    }

    public function index(): View
    {
        return view('admin.classroom.index', ['classes' => $this->repository->all()]);
    }

    public function create(): View
    {
        return view('admin.classroom.create', [
            'programs' => Program::where('is_active', true)->get(),
            'branches' => Branch::where('is_active', true)->get(),
            'primaryDays' => ClassRoom::PRIMARY_DAYS,
            'tutors' => $this->repository->getAvailableTutors(),
        ]);
    }

    public function store(StoreClassRoomRequest $request): RedirectResponse
    {
        $class = $this->repository->create($request->validated());

        return redirect()->route('classrooms.show', $class)->with('success', 'Kelas berhasil dibuat');
    }

    public function show(ClassRoom $classroom): View
    {
        return view('admin.classroom.show', ['class' => $this->repository->findWithDetails($classroom->id)]);
    }

    public function edit(ClassRoom $classroom): View
    {
        return view('admin.classroom.edit', [
            'class' => $classroom,
            'programs' => Program::where('is_active', true)->get(),
            'branches' => Branch::where('is_active', true)->get(),
            'primaryDays' => ClassRoom::PRIMARY_DAYS,
            'tutors' => $this->repository->getAvailableTutors(),
        ]);
    }

    public function update(UpdateClassRoomRequest $request, ClassRoom $classroom): RedirectResponse
    {
        $this->repository->update($classroom, $request->validated());

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(ClassRoom $classroom): RedirectResponse
    {
        if (! $classroom->canBeDeleted()) {
            return back()->withErrors('Hanya kelas planned tanpa member yang boleh dihapus');
        }
        $this->repository->delete($classroom);

        return redirect()->route('classrooms.index')->with('success', 'Kelas berhasil dihapus');
    }
}
