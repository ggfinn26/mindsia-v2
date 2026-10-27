<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ActivityPhoto;
use App\Models\Program;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function show(Program $program): View
    {
        abort_unless($program->is_active, 404);

        $otherPrograms = Program::where('is_active', true)
            ->whereKeyNot($program->getKey())
            ->get();

        $photos = ActivityPhoto::forSection('home')->limit(6)->get();

        return view('landing.program.show', compact('program', 'otherPrograms', 'photos'));
    }
}
