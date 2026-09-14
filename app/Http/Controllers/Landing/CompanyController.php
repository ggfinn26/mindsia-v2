<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ActivityPhoto;
use App\Models\Branch;
use App\Models\LandingLeader;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __invoke(Request $request)
    {
        $photos = ActivityPhoto::forSection('company')->get();

        $leaders = LandingLeader::visible()->get();

        $totalCabang = Branch::where('is_active', true)->count();
        $totalProvinsi = Branch::where('branches.is_active', true)
            ->join('areas', 'branches.areas_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->distinct('regions.province_id')
            ->count('regions.province_id');

        return view('landing.company.index', compact(
            'photos', 'leaders', 'totalCabang', 'totalProvinsi'
        ));
    }
}
