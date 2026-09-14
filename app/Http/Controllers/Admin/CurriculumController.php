<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\StoreCurriculumItemRequest;
use App\Http\Requests\Curriculum\StoreCurriculumRequest;
use App\Http\Requests\Curriculum\StoreCurriculumSessionRequest;
use App\Http\Requests\Curriculum\UpdateCurriculumItemRequest;
use App\Http\Requests\Curriculum\UpdateCurriculumRequest;
use App\Http\Requests\Curriculum\UpdateCurriculumSessionRequest;
use App\Models\Curriculum;
use App\Models\CurriculumItem;
use App\Models\CurriculumSession;
use App\Models\Program;
use App\Services\CurriculumContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CurriculumController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:curriculum.curriculum.create')];
    }

    public function __construct(
        private CurriculumContentService $service,
    ) {}

    public function index(): View
    {

        $curriculums = Curriculum::select('id', 'program_id', 'curriculum_name', 'description', 'is_active', 'created_at')
            ->with('program:id,program_name')
            ->with(['sessions' => fn ($q) => $q->select('id', 'curriculum_id')])
            ->with(['sessions.items' => fn ($q) => $q->select('id', 'curriculum_session_id')])
            ->where('is_active', true)
            ->latest()
            ->paginate(15);

        return view('admin.curriculum.index', compact('curriculums'));
    }

    public function create(): View
    {
        $programs = Program::where('is_active', true)->get();

        return view('admin.curriculum.create', compact('programs'));
    }

    public function store(StoreCurriculumRequest $request): RedirectResponse
    {
        $curriculum = $this->service->createWithSessions($request->validated());

        return redirect()
            ->route('curriculums.show', $curriculum)
            ->with('success', 'Kurikulum berhasil dibuat');
    }

    public function show(Curriculum $curriculum): View
    {
        $curriculum->load('sessions.items');

        return view('admin.curriculum.show', compact('curriculum'));
    }

    public function edit(Curriculum $curriculum): View
    {
        $curriculum->load('sessions.items');
        $programs = Program::where('is_active', true)->get();

        return view('admin.curriculum.edit', compact('curriculum', 'programs'));
    }

    public function update(UpdateCurriculumRequest $request, Curriculum $curriculum): RedirectResponse
    {
        $curriculum->update($request->validated());

        return redirect()
            ->route('curriculums.show', $curriculum)
            ->with('success', 'Kurikulum berhasil diperbarui');
    }

    public function destroy(Curriculum $curriculum): RedirectResponse
    {
        $curriculum->delete();

        return redirect()->route('curriculums.index')->with('success', 'Kurikulum berhasil dihapus');
    }

    public function storeSession(StoreCurriculumSessionRequest $request, Curriculum $curriculum): RedirectResponse
    {
        $this->service->createSession($curriculum, $request->validated());

        return redirect()
            ->route('curriculums.show', $curriculum)
            ->with('success', 'Sesi berhasil ditambahkan');
    }

    public function updateSession(UpdateCurriculumSessionRequest $request, CurriculumSession $session): RedirectResponse
    {
        $this->service->updateSession($session, $request->validated());

        return redirect()
            ->route('curriculums.show', $session->curriculum)
            ->with('success', 'Sesi berhasil diperbarui');
    }

    public function destroySession(CurriculumSession $session): RedirectResponse
    {
        $curriculum = $session->curriculum;
        $session->delete();

        return redirect()
            ->route('curriculums.show', $curriculum)
            ->with('success', 'Sesi berhasil dihapus');
    }

    public function storeItem(StoreCurriculumItemRequest $request, CurriculumSession $session): RedirectResponse
    {
        $this->service->createItem($session, $request->validated());

        return redirect()
            ->route('curriculums.show', $session->curriculum)
            ->with('success', 'Item berhasil ditambahkan');
    }

    public function updateItem(UpdateCurriculumItemRequest $request, CurriculumItem $item): RedirectResponse
    {
        $this->service->updateItem($item, $request->validated());

        return redirect()
            ->route('curriculums.show', $item->session->curriculum)
            ->with('success', 'Item berhasil diperbarui');
    }

    public function destroyItem(CurriculumItem $item): RedirectResponse
    {
        $curriculum = $item->session->curriculum;
        $item->delete();

        return redirect()
            ->route('curriculums.show', $curriculum)
            ->with('success', 'Item berhasil dihapus');
    }
}
