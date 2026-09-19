<?php

namespace App\Http\Controllers\Admin\Letter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\StoreSopDocumentRequest;
use App\Http\Requests\Letter\UpdateSopDocumentRequest;
use App\Models\SopDocument;
use App\Repositories\Letter\SopDocumentRepository;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SopDocumentController extends Controller
{
    public function __construct(
        private readonly SopDocumentRepository $repository,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        $user = request()->user();
        $roleName = $user->roles->first()?->name ?? '';
        $branchId = $user->employee?->branch_id;

        return view('letter.sop.index', [
            'documents' => $this->repository->visibleTo($roleName, $branchId),
        ]);
    }

    public function create(): View
    {
        return view('letter.sop.create');
    }

    public function store(StoreSopDocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $file = $request->file('file');

        $uploaded = $this->telegramStorage->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'sop_document',
            null,
        );

        $this->repository->create([
            'branch_id' => $data['branch_id'] ?? null,
            'category' => $data['category'],
            'title' => $data['title'],
            'document_code' => $data['document_code'] ?? null,
            'version' => $data['version'],
            'telegram_file_id' => $uploaded['file_id'],
            'storage_path' => $uploaded['file_id'],
            'effective_date' => $data['effective_date'],
            'visible_to' => $data['visible_to'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('sop-documents.index')->with('success', 'SOP berhasil diunggah.');
    }

    public function show(SopDocument $sopDocument): View
    {
        $sopDocument->load(['branch', 'uploadedBy']);

        return view('letter.sop.show', compact('sopDocument'));
    }

    public function edit(SopDocument $sopDocument): View
    {
        return view('letter.sop.edit', compact('sopDocument'));
    }

    public function update(UpdateSopDocumentRequest $request, SopDocument $sopDocument): RedirectResponse
    {
        $this->repository->update($sopDocument, $request->validated());

        return redirect()->route('sop-documents.show', $sopDocument)->with('success', 'SOP berhasil diperbarui.');
    }

    public function deactivate(SopDocument $sopDocument): RedirectResponse
    {
        $this->repository->deactivate($sopDocument);

        return redirect()->route('sop-documents.index')->with('success', 'SOP dinonaktifkan.');
    }

    public function download(SopDocument $sopDocument): mixed
    {
        abort_unless($sopDocument->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($sopDocument->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$sopDocument->title.'.pdf"',
        ]);
    }
}
