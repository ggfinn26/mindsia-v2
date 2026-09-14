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
            'telegram_file_id' => $uploaded['telegram_file_id'],
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'pic_employee_id' => $data['pic_employee_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'uploaded_by_employee_id' => $request->user()->employee->id,
        ]);

        return redirect()->route('in-letters.index')->with('success', 'Surat masuk berhasil dicatat.');
    }

    public function show(InLetter $inLetter): View
    {
        $inLetter->load(['branch', 'pic', 'uploadedBy']);

        return view('letter.in.show', compact('inLetter'));
    }

    public function edit(InLetter $inLetter): View
    {
        return view('letter.in.edit', compact('inLetter'));
    }

    public function update(UpdateInLetterRequest $request, InLetter $inLetter): RedirectResponse
    {
        $this->repository->update($inLetter, $request->validated());

        return redirect()->route('in-letters.show', $inLetter)->with('success', 'Surat masuk berhasil diperbarui.');
    }

    public function destroy(InLetter $inLetter): RedirectResponse
    {
        $this->repository->delete($inLetter);

        return redirect()->route('in-letters.index')->with('success', 'Surat masuk berhasil dihapus.');
    }

    public function download(InLetter $inLetter): mixed
    {
        abort_unless($inLetter->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($inLetter->telegram_file_id);

        return response($content, 200, [
            'Content-Type' => $inLetter->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.($inLetter->original_name ?? 'surat-masuk.pdf').'"',
        ]);
    }
}
