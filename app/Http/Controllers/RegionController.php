<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use App\Repositories\ProvinceRepository;
use App\Repositories\RegionRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function __construct(
        private readonly RegionRepository $repository,
        private readonly ProvinceRepository $provinceRepository,
    ) {}

    public function index(): View
    {
        return view('organization.region.index', [
            'regions' => $this->repository->all(),
        ]);
    }

    public function store(StoreRegionRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('regions.index')->with('success', 'Region berhasil ditambahkan.');
    }

    public function update(UpdateRegionRequest $request, Region $region): RedirectResponse
    {
        $this->repository->update($region, $request->validated());

        return redirect()->route('regions.index')->with('success', 'Region berhasil diperbarui.');
    }

    public function destroy(Region $region): RedirectResponse
    {
        $this->repository->delete($region);

        return redirect()->route('regions.index')->with('success', 'Region berhasil dihapus.');
    }
}
