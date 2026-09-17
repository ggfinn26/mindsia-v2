<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClassRoom\StoreClassTestRequest;
use App\Http\Requests\ClassRoom\UpdateClassTestRequest;
use App\Models\ClassRoom;
use App\Models\ClassTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClassTestController extends Controller
{
    public function index(): View
    {
        $this->authorize('class.test.create');

        $tests = ClassTest::with('classRoom.program', 'classRoom.branch')
            ->withCount('memberTestResults')
            ->latest('date')
            ->paginate(20);

        return view('admin.class-test.index', compact('tests'));
    }

    public function create(ClassRoom $classroom): View
    {
        $this->authorize('class.test.create');

        return view('class.test.create', ['classroom' => $classroom]);
    }

    public function store(StoreClassTestRequest $request, ClassRoom $classroom): RedirectResponse
    {
        DB::transaction(function () use ($request, $classroom) {
            $test = ClassTest::create(array_merge(
                $request->safe()->except('criteria'),
                ['class_id' => $classroom->id, 'program_id' => $classroom->program_id]
            ));

            foreach ($request->validated('criteria', []) as $criterion) {
                $test->scoringCriteria()->create($criterion);
            }
        });

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Test berhasil ditambahkan.');
    }

    public function edit(ClassRoom $classroom, ClassTest $classTest): View
    {
        $this->authorize('class.test.update');

        return view('class.test.edit', [
            'classroom' => $classroom,
            'test' => $classTest->load('scoringCriteria'),
        ]);
    }

    public function update(UpdateClassTestRequest $request, ClassRoom $classroom, ClassTest $classTest): RedirectResponse
    {
        $classTest->update($request->validated());

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Test berhasil diperbarui.');
    }

    public function destroy(ClassRoom $classroom, ClassTest $classTest): RedirectResponse
    {
        $this->authorize('class.test.delete');

        $classTest->delete();

        return redirect()->route('classrooms.show', $classroom)->with('success', 'Test berhasil dihapus.');
    }
}
