<?php

namespace App\Http\Controllers\Admin\Recruitment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recruitment\NegotiateOfferingLetterRequest;
use App\Http\Requests\Recruitment\StoreOfferingLetterRequest;
use App\Http\Requests\Recruitment\UpdateOfferingLetterRequest;
use App\Models\JobApplication;
use App\Models\OfferingLetter;
use App\Repositories\Recruitment\OfferingLetterRepository;
use App\Services\Recruitment\RecruitmentStageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfferingLetterController extends Controller
{
    public function __construct(
        private readonly OfferingLetterRepository $repository,
        private readonly RecruitmentStageService $stageService,
    ) {}

    public function store(StoreOfferingLetterRequest $request, JobApplication $jobApplication): RedirectResponse
    {
        // advance status → offering + buat offering letter
        $this->stageService->createOfferingLetter($jobApplication, $request->validated(), $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $jobApplication)->with('success', 'Surat penawaran dibuat.');
    }

    public function update(UpdateOfferingLetterRequest $request, OfferingLetter $offeringLetter): RedirectResponse
    {
        $this->repository->update($offeringLetter, $request->validated());

        return redirect()->back()->with('success', 'Surat penawaran diperbarui.');
    }

    public function negotiate(NegotiateOfferingLetterRequest $request, OfferingLetter $offeringLetter): RedirectResponse
    {
        // set status negotiating + update agreed_salary jika sudah deal
        $this->repository->negotiate($offeringLetter, $request->validated());

        return redirect()->back()->with('success', 'Negosiasi dicatat.');
    }

    public function accept(Request $request, OfferingLetter $offeringLetter): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.offering_letter.update'), 403);

        $this->stageService->acceptOffering($offeringLetter, $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $offeringLetter->application)->with('success', 'Penawaran diterima.');
    }

    public function decline(Request $request, OfferingLetter $offeringLetter): RedirectResponse
    {
        abort_unless($request->user()->can('recruitment.offering_letter.update'), 403);

        $this->stageService->declineOffering($offeringLetter, $request->user()->employee->id);

        return redirect()->route('recruitment.application.show', $offeringLetter->application)->with('success', 'Penawaran ditolak pelamar.');
    }

    public function downloadPdf(Request $request, OfferingLetter $offeringLetter): Response
    {
        abort_unless($request->user()->can('recruitment.offering_letter.export'), 403);
        abort_unless($offeringLetter->pdf_path, 404);

        $real = realpath(storage_path((string) $offeringLetter->pdf_path));
        abort_unless($real && str_starts_with($real, (string) realpath(storage_path('app'))), 403);

        return response()->file($real);
    }
}
