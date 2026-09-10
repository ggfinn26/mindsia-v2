<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use App\Repositories\InstitutionRepository;
use App\Repositories\RegionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function __construct(
        private readonly InstitutionRepository $repository,
        private readonly RegionRepository $regionRepository,
    ) {}

    public function index(): View
    {
        return view('organization.institution.index', [
            'institutions' => $this->repository->all(),
        ]);
    }

    public function store(StoreInstitutionRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('institutions.index')->with('success', 'Institusi berhasil ditambahkan.');
    }

    public function update(UpdateInstitutionRequest $request, Institution $institution): RedirectResponse
    {
        $this->repository->update($institution, $request->validated());

        return redirect()->route('institutions.index')->with('success', 'Institusi berhasil diperbarui.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        $this->authorize('organization.institution.delete');

        $this->repository->delete($institution);

        return redirect()->route('institutions.index')->with('success', 'Institusi berhasil dihapus.');
    }
}
