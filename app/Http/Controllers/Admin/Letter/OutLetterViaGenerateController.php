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
        return view('letter.out-generate.create', [
            'preselectedTemplateId' => request()->integer('template_id') ?: null,
        ]);
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

    public function show(OutLetterViaGenerate $out_letters_generate): View
    {
        $out_letters_generate->load(['template', 'branch', 'signer', 'createdBy', 'publishedBy']);

        return view('letter.out-generate.show', ['outLetterViaGenerate' => $out_letters_generate]);
    }

    public function edit(OutLetterViaGenerate $out_letters_generate): View
    {
        abort_unless($out_letters_generate->isDraft(), 403, 'Surat sudah diterbitkan dan tidak dapat diedit.');

        return view('letter.out-generate.edit', ['outLetterViaGenerate' => $out_letters_generate]);
    }

    public function update(UpdateOutLetterViaGenerateRequest $request, OutLetterViaGenerate $out_letters_generate): RedirectResponse
    {
        $this->repository->update($out_letters_generate, $request->validated());

        return redirect()->route('out-letters-generate.show', $out_letters_generate)->with('success', 'Surat berhasil diperbarui.');
    }

    public function publish(PublishOutLetterViaGenerateRequest $request, OutLetterViaGenerate $out_letters_generate): RedirectResponse
    {
        $this->letterService->publish($out_letters_generate, $request->user()->employee->id);

        return redirect()->route('out-letters-generate.show', $out_letters_generate)->with('success', 'Surat berhasil diterbitkan.');
    }

    public function download(OutLetterViaGenerate $out_letters_generate): mixed
    {
        abort_unless($out_letters_generate->telegram_file_id, 404);

        $content = $this->telegramStorage->downloadFile($out_letters_generate->telegram_file_id);
        $filename = $out_letters_generate->letter_type.'_'.($out_letters_generate->letter_number ?? $out_letters_generate->id).'.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
