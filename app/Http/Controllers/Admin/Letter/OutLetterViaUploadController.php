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
            'telegram_file_id' => $uploaded['file_id'],
            'storage_path' => $uploaded['file_id'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'notes' => $data['notes'] ?? null,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('out-letters-upload.index')->with('success', 'Surat keluar berhasil diunggah.');
    }

    public function show(OutLetterViaUpload $out_letters_upload): View
    {
        $out_letters_upload->load(['branch', 'uploadedBy']);

        return view('letter.out-upload.show', ['outLetterViaUpload' => $out_letters_upload]);
    }

    public function edit(OutLetterViaUpload $out_letters_upload): View
    {
        return view('letter.out-upload.edit', ['outLetterViaUpload' => $out_letters_upload]);
    }

    public function update(UpdateOutLetterViaUploadRequest $request, OutLetterViaUpload $out_letters_upload): RedirectResponse
    {
        $this->repository->update($out_letters_upload, $request->validated());

        return redirect()->route('out-letters-upload.show', $out_letters_upload)->with('success', 'Surat berhasil diperbarui.');
    }

    public function destroy(OutLetterViaUpload $out_letters_upload): RedirectResponse
    {
        $this->repository->delete($out_letters_upload);

        return redirect()->route('out-letters-upload.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function download(OutLetterViaUpload $out_letters_upload): mixed
    {
        abort_unless($out_letters_upload->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($out_letters_upload->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => $out_letters_upload->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.($out_letters_upload->original_name ?? 'surat.pdf').'"',
        ]);
    }
}
