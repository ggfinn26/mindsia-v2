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
            $telegramFileId = $uploaded['telegram_file_id'];
        }

        $this->repository->create([
            'template_code' => $data['template_code'],
            'template_name' => $data['template_name'],
            'letter_category' => $data['letter_category'],
            'letter_number_format' => $data['letter_number_format'] ?? null,
            'telegram_file_id' => $telegramFileId,
            'is_active' => $data['is_active'] ?? true,
            'created_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil disimpan.');
    }

    public function show(LetterTemplate $letterTemplate): View
    {
        return view('letter.template.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate): View
    {
        return view('letter.template.edit', compact('letterTemplate'));
    }

    public function update(UpdateLetterTemplateRequest $request, LetterTemplate $letterTemplate): RedirectResponse
    {
        $data = $request->validated();
        $updates = [
            'template_code' => $data['template_code'],
            'template_name' => $data['template_name'],
            'letter_category' => $data['letter_category'],
            'letter_number_format' => $data['letter_number_format'] ?? null,
            'is_active' => $data['is_active'] ?? $letterTemplate->is_active,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $uploaded = $this->telegramStorage->uploadFile(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                'letter_template',
                $letterTemplate->id,
            );
            $updates['telegram_file_id'] = $uploaded['telegram_file_id'];
        }

        $this->repository->update($letterTemplate, $updates);

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(LetterTemplate $letterTemplate): RedirectResponse
    {
        abort_unless(auth()->user()->can('letter.template.manage'), 403);
        abort_if($letterTemplate->generatedLetters()->exists(), 422, 'Template sudah digunakan, tidak bisa dihapus.');

        $letterTemplate->delete();

        return redirect()->route('letter-templates.index')->with('success', 'Template berhasil dihapus.');
    }

    public function toggleActive(LetterTemplate $letterTemplate): RedirectResponse
    {
        $this->repository->toggleActive($letterTemplate);

        return redirect()->route('letter-templates.index')->with('success', 'Status template diubah.');
    }

    public function manualVars(Request $request): JsonResponse
    {
        $template = $this->repository->find($request->integer('template_id'));

        return response()->json([
            'manual_vars' => $this->letterService->extractManualPlaceholders($template),
        ]);
    }
}
