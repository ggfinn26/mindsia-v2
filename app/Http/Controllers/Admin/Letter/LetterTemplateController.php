<?php

namespace App\Http\Controllers\Admin\Letter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\StoreLetterTemplateRequest;
use App\Http\Requests\Letter\UpdateLetterTemplateRequest;
use App\Models\LetterTemplate;
use App\Repositories\Letter\LetterTemplateRepository;
use App\Services\Letter\LetterService;
use App\Services\TelegramStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterTemplateController extends Controller
{
    public function __construct(
        private readonly LetterTemplateRepository $repository,
        private readonly LetterService $letterService,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        return view('letter.template.index', [
            'templates' => $this->repository->all(),
        ]);
    }

    public function create(): View
    {
        return view('letter.template.create');
    }

    public function store(StoreLetterTemplateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $telegramFileId = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'letter_template',
                null,
            );
            $telegramFileId = $uploaded['file_id'];
        }

        $this->repository->create([
            'template_code' => $data['template_code'],
            'template_name' => $data['template_name'],
            'letter_category' => $data['letter_category'],
            'letter_number_format' => $data['letter_number_format'] ?? null,
            'telegram_file_id' => $telegramFileId,
            'storage_path' => $telegramFileId,
            'is_active' => $data['is_active'] ?? true,
            'created_by_employee_id' => $request->user()->employee?->id,
        ]);

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil disimpan.');
    }

    public function show(LetterTemplate $letter_template): View
    {
        $letter_template->load('generatedLetters');

        return view('letter.template.show', ['letterTemplate' => $letter_template]);
    }

    public function edit(LetterTemplate $letter_template): View
    {
        return view('letter.template.edit', ['letterTemplate' => $letter_template]);
    }

    public function update(UpdateLetterTemplateRequest $request, LetterTemplate $letter_template): RedirectResponse
    {
        $data = $request->validated();
        $updates = [
            'template_code' => $data['template_code'],
            'template_name' => $data['template_name'],
            'letter_category' => $data['letter_category'],
            'letter_number_format' => $data['letter_number_format'] ?? null,
            'is_active' => $data['is_active'] ?? $letter_template->is_active,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'letter_template',
                $letter_template->id,
            );
            $updates['telegram_file_id'] = $uploaded['file_id'];
            $updates['storage_path'] = $uploaded['file_id'];
        }

        $this->repository->update($letter_template, $updates);

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(LetterTemplate $letter_template): RedirectResponse
    {
        abort_unless(auth()->user()->can('letter.template.manage'), 403);
        abort_if($letter_template->generatedLetters()->exists(), 422, 'Template sudah digunakan, tidak bisa dihapus.');

        $letter_template->delete();

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil dihapus.');
    }

    public function toggleActive(LetterTemplate $letter_template): RedirectResponse
    {
        $this->repository->toggleActive($letter_template);

        return redirect()->route('letter-templates.index')->with('success', 'Status template diubah.');
    }

    public function manualVars(Request $request): JsonResponse
    {
        $template = $this->repository->find($request->integer('template_id'));

        return response()->json([
            'manual_vars' => $this->letterService->extractManualPlaceholders($template),
        ]);
    }

    public function templateVars(Request $request): JsonResponse
    {
        $template = $this->repository->find($request->integer('template_id'));

        return response()->json([
            'vars' => $template ? $this->letterService->classifyTemplateVars($template) : [],
        ]);
    }
}
