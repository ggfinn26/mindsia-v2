<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProgramController extends Controller implements HasMiddleware
{
    public function __construct(private TelegramStorageService $telegramStorage) {}

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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        if ($request->hasFile('receipt_template')) {
            $file = $request->file('receipt_template');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'program_receipt_template',
                null,
            );
            $data['receipt_template_path'] = $uploaded['file_id'];
        }

        Program::create($data);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dibuat.');
    }

    public function show(Program $program): View
    {
        $program->load('curriculum', 'branchQuotas.branch', 'paymentPlans');

        return view('program.show', compact('program'));
    }

    public function edit(Program $program): View
    {
        return view('program.edit', compact('program'));
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $data['image_path'] = $request->file('image')->store('programs', 'public');
        }

        if ($request->hasFile('receipt_template')) {
            $file = $request->file('receipt_template');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'program_receipt_template',
                null,
            );
            $data['receipt_template_path'] = $uploaded['file_id'];
        }

        $program->update($data);

        return redirect()->route('programs.show', $program)->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        if ($program->registrations()->exists()) {
            return back()->with('error', 'Program tidak bisa dinonaktifkan karena masih ada registrasi member.');
        }

        $program->update(['is_active' => false]);

        return redirect()->route('programs.index')->with('success', 'Program berhasil dinonaktifkan.');
    }
}
