<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Institution;
use App\Models\Province;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function getActiveProvinces(): JsonResponse
    {
        $provinces = Province::whereHas('regions.areas.branches')->get(['id', 'name']);

        return response()->json($provinces);
    }

    public function getActiveRegions(Request $request): JsonResponse
    {
        $provinceId = $request->query('province_id');
        if (! $provinceId) {
            return response()->json([]);
        }

        $regions = Region::where('province_id', $provinceId)
            ->whereHas('areas.branches')
            ->get(['id', 'name']);

        return response()->json($regions);
    }

    public function getBranches(Request $request): JsonResponse
    {
        $provinceId = $request->query('province_id');
        if (! $provinceId) {
            return response()->json([]);
        }

        $branches = Branch::active()
            ->whereHas('area.region', fn ($q) => $q->where('province_id', $provinceId))
            ->orderBy('branch_name')
            ->get(['id', 'branch_name', 'code_branches']);

        return response()->json($branches);
    }

    public function getInstitutions(Request $request): JsonResponse
    {
        // Support pencarian berdasarkan province_id atau region_id
        $provinceId = $request->query('province_id');
        $regionId = $request->query('region_id');

        $query = Institution::query();

        if ($regionId) {
            $query->where('regions_id', $regionId);
        } elseif ($provinceId) {
            $query->whereHas('region', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        } else {
            return response()->json([]);
        }

        $jenjangParam = $request->query('jenjang');
        if ($jenjangParam) {
            $jenjangList = array_filter(array_map('trim', explode(',', $jenjangParam)));
            if ($jenjangList) {
                $query->whereIn('jenjang_institution', $jenjangList);
            }
        }

        $institutions = $query->orderBy('institution_name')->get(['id', 'institution_name']);

        return response()->json($institutions);
    }

    public function getIndonesianProvinces(): JsonResponse
    {
        $path = base_path('../data-indonesia/provinsi.json');
        if (! file_exists($path)) {
            return response()->json([]);
        }
        $data = json_decode(file_get_contents($path), true);

        return response()->json($data);
    }

    public function getIndonesianCities(Request $request): JsonResponse
    {
        $provinceId = $request->query('province_id');
        if (! $provinceId || ! preg_match('/^\d{1,4}$/', (string) $provinceId)) {
            return response()->json([]);
        }

        $cities = [];

        $kotaPath = base_path('../data-indonesia/kota/'.$provinceId.'.json');
        if (file_exists($kotaPath)) {
            $kotaData = json_decode(file_get_contents($kotaPath), true) ?: [];
            foreach ($kotaData as &$k) {
                $k['nama'] = 'Kota '.$k['nama'];
            }
            $cities = array_merge($cities, $kotaData);
        }

        $kabPath = base_path('../data-indonesia/kabupaten/'.$provinceId.'.json');
        if (file_exists($kabPath)) {
            $kabData = json_decode(file_get_contents($kabPath), true) ?: [];
            foreach ($kabData as &$k) {
                $k['nama'] = 'Kabupaten '.$k['nama'];
            }
            $cities = array_merge($cities, $kabData);
        }

        // Sort by name
        usort($cities, function ($a, $b) {
            return strcmp($a['nama'], $b['nama']);
        });

        return response()->json($cities);
    }
}
