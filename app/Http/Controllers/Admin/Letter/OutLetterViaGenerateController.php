<?php

namespace App\Http\Controllers\Admin\Letter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Letter\PublishOutLetterViaGenerateRequest;
use App\Http\Requests\Letter\StoreOutLetterViaGenerateRequest;
use App\Http\Requests\Letter\UpdateOutLetterViaGenerateRequest;
use App\Models\OutLetterViaGenerate;
use App\Repositories\Letter\OutLetterViaGenerateRepository;
use App\Services\Letter\LetterService;
use App\Services\TelegramStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OutLetterViaGenerateController extends Controller
{
    public function __construct(
        private readonly OutLetterViaGenerateRepository $repository,
        private readonly LetterService $letterService,
        private readonly TelegramStorageService $telegramStorage,
    ) {}

    public function index(): View
    {
        return view('letter.out-generate.index');
    }

    public function create(): View
    {
        return view('letter.out-generate.create');
    }

    public function store(StoreOutLetterViaGenerateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $employee = $request->user()->employee;

        $letter = $this->letterService->generate(
            $data['letter_template_id'],
            [
                'branch_id' => $data['branch_id'],
                'employee_id' => $data['employee_id'] ?? null,
                'signer_employee_id' => $data['signer_employee_id'] ?? null,
                'institution' => $data['institution'] ?? null,
            ],
            $data['manual_vars'] ?? [],
            $employee->id,
        );

        $letter->update([
            'recipient' => $data['recipient'] ?? null,
            'subject' => $data['subject'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('out-letters-generate.show', $letter)->with('success', 'Surat berhasil dibuat (DRAFT).');
    }

    public function show(OutLetterViaGenerate $outLetterViaGenerate): View
    {
        $outLetterViaGenerate->load(['template', 'branch', 'signer', 'createdBy', 'publishedBy']);

        return view('letter.out-generate.show', compact('outLetterViaGenerate'));
    }

    public function edit(OutLetterViaGenerate $outLetterViaGenerate): View
    {
        abort_unless($outLetterViaGenerate->isDraft(), 403, 'Surat sudah diterbitkan dan tidak dapat diedit.');

        return view('letter.out-generate.edit', compact('outLetterViaGenerate'));
    }

    public function update(UpdateOutLetterViaGenerateRequest $request, OutLetterViaGenerate $outLetterViaGenerate): RedirectResponse
    {
        $this->repository->update($outLetterViaGenerate, $request->validated());

        return redirect()->route('out-letters-generate.show', $outLetterViaGenerate)->with('success', 'Surat berhasil diperbarui.');
    }

    public function publish(PublishOutLetterViaGenerateRequest $request, OutLetterViaGenerate $outLetterViaGenerate): RedirectResponse
    {
        $this->letterService->publish($outLetterViaGenerate, $request->user()->employee->id);

        return redirect()->route('out-letters-generate.show', $outLetterViaGenerate)->with('success', 'Surat berhasil diterbitkan.');
    }

    public function download(OutLetterViaGenerate $outLetterViaGenerate): mixed
    {
        abort_unless($outLetterViaGenerate->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($outLetterViaGenerate->telegram_file_id);
        $filename = $outLetterViaGenerate->letter_type.'_'.($outLetterViaGenerate->letter_number ?? $outLetterViaGenerate->id).'.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
