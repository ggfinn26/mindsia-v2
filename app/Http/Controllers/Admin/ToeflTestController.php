<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Toefl\StoreToeflTestRequest;
use App\Http\Requests\Toefl\UpdateToeflTestRequest;
use App\Models\ToeflTest;
use App\Repositories\Toefl\ToeflTestRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToeflTestController extends Controller
{
    public function __construct(
        private readonly ToeflTestRepository $repository,
    ) {}

    public function index(): View
    {
        return view('toefl.test.index', [
            'tests' => $this->repository->list(),
        ]);
    }

    public function create(): View
    {
        return view('toefl.test.create');
    }

    public function store(StoreToeflTestRequest $request): RedirectResponse
    {
        $test = $this->repository->create(array_merge($request->validated(), [
            'created_by_employee_id' => $request->user()->employee->id,
            'listening_time_limit' => $request->input('listening_time_limit', ToeflTest::DEFAULT_TIME_LIMIT['listening']),
            'structure_time_limit' => $request->input('structure_time_limit', ToeflTest::DEFAULT_TIME_LIMIT['structure']),
            'reading_time_limit' => $request->input('reading_time_limit', ToeflTest::DEFAULT_TIME_LIMIT['reading']),
        ]));

        return redirect()->route('toefl-tests.show', $test)->with('success', 'Test berhasil dibuat.');
    }

    public function show(ToeflTest $toeflTest): View
    {
        $toeflTest->load([
            'passages',
            'questions' => fn ($q) => $q->orderBy('section')->orderBy('display_order'),
        ]);

        return view('toefl.test.show', compact('toeflTest'));
    }

    public function edit(ToeflTest $toeflTest): View
    {
        abort_unless($toeflTest->status === ToeflTest::STATUS_DRAFT, 403);

        return view('toefl.test.edit', compact('toeflTest'));
    }

    public function update(UpdateToeflTestRequest $request, ToeflTest $toeflTest): RedirectResponse
    {
        $this->repository->update($toeflTest, $request->validated());

        return redirect()->route('toefl-tests.show', $toeflTest)->with('success', 'Test diperbarui.');
    }

    public function publish(Request $request, ToeflTest $toeflTest): RedirectResponse
    {
        abort_unless($request->user()->can('toefl.test.publish'), 403);

        $this->repository->publish($toeflTest, $request->user()->employee->id);

        return redirect()->route('toefl-tests.show', $toeflTest)->with('success', 'Test dipublikasikan.');
    }

    public function destroy(ToeflTest $toeflTest): RedirectResponse
    {
        abort_if($toeflTest->sessions()->exists(), 422, 'Test sudah memiliki session — tidak bisa dihapus.');

        $toeflTest->delete();

        return redirect()->route('toefl-tests.index')->with('success', 'Test dihapus.');
    }
}
