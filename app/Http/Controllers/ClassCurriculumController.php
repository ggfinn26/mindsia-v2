<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\CurriculumItem;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ClassCurriculumController extends Controller
{
    private function validateStoragePath(string $relativePath): string
    {
        $base = realpath(storage_path('app'));
        $full = realpath($base.DIRECTORY_SEPARATOR.$relativePath);

        abort_if(! $full || strpos($full, $base.DIRECTORY_SEPARATOR) !== 0, 404);

        return $full;
    }

    public function index(ClassRoom $classroom): View
    {
        $this->authorize('viewCurriculum', $classroom);

        $curriculum = $classroom->program->curriculum;
        abort_if(! $curriculum, 404);

        return view('curriculum.class-index', [
            'classroom' => $classroom,
            'curriculum' => $curriculum,
            'sessions' => $curriculum->sessions()->with('items')->get(),
        ]);
    }

    public function show(ClassRoom $classroom, CurriculumItem $item): View
    {
        $this->authorize('viewItem', [$classroom, $item]);

        $session = $item->session;
        $curriculum = $session->curriculum;

        return view('curriculum.class-show', [
            'classroom' => $classroom,
            'curriculum' => $curriculum,
            'session' => $session,
            'item' => $item,
        ]);
    }

    public function downloadFile(ClassRoom $classroom, CurriculumItem $item): Response
    {
        $this->authorize('viewItem', [$classroom, $item]);

        abort_if($item->material_type !== 'file' || ! $item->material_value, 404);

        $path = $this->validateStoragePath($item->material_value);

        return response()->download($path);
    }
}
