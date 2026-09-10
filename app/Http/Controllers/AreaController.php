<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;
use App\Repositories\AreaRepository;
use App\Repositories\RegionRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function __construct(
        private readonly AreaRepository $repository,
        private readonly RegionRepository $regionRepository,
    ) {}

    public function index(): View
    {
        return view('organization.area.index', [
            'areas' => $this->repository->all(),
        ]);
    }

    public function store(StoreAreaRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('areas.index')->with('success', 'Area berhasil ditambahkan.');
    }

    public function update(UpdateAreaRequest $request, Area $area): RedirectResponse
    {
        $this->repository->update($area, $request->validated());

        return redirect()->route('areas.index')->with('success', 'Area berhasil diperbarui.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        $this->authorize('organization.area.delete');

        try {
            $this->repository->delete($area);

            return redirect()->route('areas.index')->with('success', 'Area berhasil dihapus.');
        } catch (\RuntimeException|QueryException $e) {
            return redirect()->route('areas.index')->with('error', $e->getMessage());
        }
    }
}
