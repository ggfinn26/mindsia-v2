<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProgramController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:curriculum.program.create'),
        ];
    }

    public function index(): View
    {
        $programs = Program::with('curriculum')->orderBy('program_name')->get();

        return view('program.index', compact('programs'));
    }

    public function create(): View
    {
        return view('program.create');
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        Program::create($request->validated());

        return redirect()->route('programs.index')->with('success', 'Program berhasil dibuat.');
    }

    public function show(Program $program): View
    {
        $program->load('curriculum', 'branchQuotas.branch');

        return view('program.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        return view('program.edit', compact('program'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->validated());

        return redirect()->route('programs.show', $program)->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->classes()->where('status', '!=', 'completed')->exists()) {
            return back()->with('error', 'Program tidak bisa dihapus karena masih ada kelas aktif.');
        }

        $program->update(['is_active' => false]);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dinonaktifkan.');
    }
}
