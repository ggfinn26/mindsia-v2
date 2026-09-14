<?php

namespace App\Http\Controllers\Admin\Letter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\StoreOutLetterViaUploadRequest;
use App\Http\Requests\Letter\UpdateOutLetterViaUploadRequest;
use App\Models\OutLetterViaUpload;
use App\Repositories\Letter\OutLetterViaUploadRepository;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OutLetterViaUploadController extends Controller
{
    public function __construct(
        private readonly OutLetterViaUploadRepository $repository,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        return view('letter.out-upload.index');
    }

    public function create(): View
    {
        return view('letter.out-upload.create');
    }

    public function store(StoreOutLetterViaUploadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $file = $request->file('file');

        $uploaded = $this->telegramStorage->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'out_letter_upload',
            null,
        );

        $this->repository->create([
            'branch_id' => $data['branch_id'],
            'letter_type' => $data['letter_type'] ?? null,
            'letter_number' => $data['letter_number'] ?? null,
            'letter_date' => $data['letter_date'] ?? null,
            'recipient' => $data['recipient'] ?? null,
            'subject' => $data['subject'] ?? null,
            'telegram_file_id' => $uploaded['telegram_file_id'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'notes' => $data['notes'] ?? null,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('out-letters-upload.index')->with('success', 'Surat keluar berhasil diunggah.');
    }

    public function show(OutLetterViaUpload $outLetterViaUpload): View
    {
        $outLetterViaUpload->load(['branch', 'uploadedBy']);

        return view('letter.out-upload.show', compact('outLetterViaUpload'));
    }

    public function edit(OutLetterViaUpload $outLetterViaUpload): View
    {
        return view('letter.out-upload.edit', compact('outLetterViaUpload'));
    }

    public function update(UpdateOutLetterViaUploadRequest $request, OutLetterViaUpload $outLetterViaUpload): RedirectResponse
    {
        $this->repository->update($outLetterViaUpload, $request->validated());

        return redirect()->route('out-letters-upload.show', $outLetterViaUpload)->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy(OutLetterViaUpload $outLetterViaUpload): RedirectResponse
    {
        $this->repository->delete($outLetterViaUpload);

        return redirect()->route('out-letters-upload.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function download(OutLetterViaUpload $outLetterViaUpload): mixed
    {
        abort_unless($outLetterViaUpload->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($outLetterViaUpload->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => $outLetterViaUpload->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.($outLetterViaUpload->original_name ?? 'surat.pdf').'"',
        ]);
    }
}
