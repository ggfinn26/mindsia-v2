<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ActivityPhoto;
use App\Models\Branch;
use App\Models\LandingTestimonial;
use App\Models\MemberData;
use App\Models\Program;

class HomeController extends Controller
{
    public function __invoke()
    {
        $landingPrograms = Program::where('is_active', true)->get();

        $galleries = ActivityPhoto::forSection('home')->get();

        $testimonials = LandingTestimonial::visible()->get()
            ->map(fn ($t) => (object) [
                'type' => $t->type->value,
                'name' => $t->name,
                'quote' => $t->quote,
                'program' => $t->program,
                'city' => $t->city,
                'image_url' => $t->image_url,
            ]);

        $totalCabang = Branch::where('is_active', true)->count();

        $totalProvinsi = Branch::where('branches.is_active', true)
            ->join('areas', 'branches.areas_id', '=', 'areas.id')
            ->join('regions', 'areas.region_id', '=', 'regions.id')
            ->distinct('regions.province_id')
            ->count('regions.province_id');

        $totalMembers = MemberData::count();

        return view('welcome', compact(
            'landingPrograms',
            'galleries',
            'testimonials',
            'totalCabang',
            'totalProvinsi',
            'totalMembers'
        ));
    }
}
