<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\UpdateInstitutionRequest;
use App\Models\Institution;
use App\Models\Province;
use App\Repositories\InstitutionRepository;
use App\Repositories\RegionRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class InstitutionController extends Controller
{
    public function __construct(
        private readonly InstitutionRepository $repository,
        private readonly RegionRepository $regionRepository,
    ) {}

    public function index(Request $request): View
    {
        $query = Institution::with('region.province');

        if ($request->filled('search')) {
            $query->where('institution_name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang_institution', $request->jenjang);
        }

        if ($request->filled('province_id')) {
            $query->whereHas('region', function ($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        $sort = $request->query('sort', 'institution_name');
        $direction = $request->query('direction', 'asc');

        if ($sort === 'region') {
            $query->join('regions', 'institutions.regions_id', '=', 'regions.id')
                ->orderBy('regions.name', $direction)
                ->select('institutions.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $totalCount = $query->count();

        $perPage = 20;
        $maxPages = 100;
        $paginatorTotal = min($totalCount, $maxPages * $perPage);
        $page = Paginator::resolveCurrentPage('page');

        $items = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        $institutions = new LengthAwarePaginator($items, $paginatorTotal, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('organization.institution.index', [
            'institutions' => $institutions,
            'totalCount' => $totalCount,
            'provinces' => Province::orderBy('name')->get(),
            'jenjangs' => ['SD', 'SMP', 'SMA', 'PERGURUAN TINGGI'],
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

        try {
            $this->repository->delete($institution);

            return redirect()->route('institutions.index')->with('success', 'Institusi berhasil dihapus.');
        } catch (\RuntimeException|QueryException $e) {
            return redirect()->route('institutions.index')->with('error', $e->getMessage());
        }
    }
}
