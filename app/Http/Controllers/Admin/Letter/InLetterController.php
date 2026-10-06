<?php

namespace App\Http\Controllers\Admin\Letter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\StoreInLetterRequest;
use App\Http\Requests\Letter\UpdateInLetterRequest;
use App\Models\InLetter;
use App\Repositories\Letter\InLetterRepository;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InLetterController extends Controller
{
    public function __construct(
        private readonly InLetterRepository $repository,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        return view('letter.in.index');
    }

    public function create(): View
    {
        return view('letter.in.create');
    }

    public function store(StoreInLetterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $file = $request->file('file');

        $uploaded = $this->telegramStorage->uploadFile(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            'in_letter',
            null,
        );

        $this->repository->create([
            'branch_id' => $data['branch_id'],
            'sender_name' => $data['sender_name'],
            'letter_date' => $data['letter_date'] ?? null,
            'receive_date' => $data['receive_date'],
            'letter_number' => $data['letter_number'] ?? null,
            'subject' => $data['subject'],
            'telegram_file_id' => $uploaded['file_id'],
            'storage_path' => $uploaded['file_id'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'pic_employee_id' => $data['pic_employee_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('in-letters.index')->with('success', 'Surat masuk berhasil dicatat.');
    }

    public function show(InLetter $in_letter): View
    {
        $in_letter->load(['branch', 'pic', 'uploadedBy']);

        return view('letter.in.show', ['inLetter' => $in_letter]);
    }

    public function edit(InLetter $in_letter): View
    {
        return view('letter.in.edit', ['inLetter' => $in_letter]);
    }

    public function update(UpdateInLetterRequest $request, InLetter $in_letter): RedirectResponse
    {
        $this->repository->update($in_letter, $request->validated());

        return redirect()->route('in-letters.show', $in_letter)->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(InLetter $in_letter): RedirectResponse
    {
        $this->repository->delete($in_letter);

        return redirect()->route('in-letters.index')->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function download(InLetter $in_letter): mixed
    {
        abort_unless($in_letter->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($in_letter->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => $in_letter->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.($in_letter->original_name ?? 'surat-masuk.pdf').'"',
        ]);
    }
}
