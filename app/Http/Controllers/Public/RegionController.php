<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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

        $institutions = $query->get(['id', 'institution_name']);

        return response()->json($institutions);
    }
}
