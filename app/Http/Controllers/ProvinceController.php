<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvinceRequest;
use App\Http\Requests\UpdateProvinceRequest;
use App\Models\Province;
use App\Repositories\ProvinceRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function __construct(
        private readonly ProvinceRepository $repository
    ) {}

    public function index(): View
    {
        return view('organization.province.index', [
            'provinces' => $this->repository->all(),
        ]);
    }

    public function store(StoreProvinceRequest $request): RedirectResponse
    {
        $this->repository->create($request->validated());

        return redirect()->route('provinces.index')->with('success', 'Provinsi berhasil ditambahkan.');
    }

    public function update(UpdateProvinceRequest $request, Province $province): RedirectResponse
    {
        $this->repository->update($province, $request->validated());

        return redirect()->route('provinces.index')->with('success', 'Provinsi berhasil diperbarui.');
    }

    public function destroy(Province $province): RedirectResponse
    {
        $this->authorize('organization.province.delete');

        try {
            $this->repository->delete($province);

            return redirect()->route('provinces.index')->with('success', 'Provinsi berhasil dihapus.');
        } catch (\RuntimeException|QueryException $e) {
            return redirect()->route('provinces.index')->with('error', $e->getMessage());
        }
    }
}
