<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflDirectionAudio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToeflDirectionAudioController extends Controller
{
    public function index(): View
    {
        $audios = ToeflDirectionAudio::all()->keyBy('part');

        return view('toefl.direction-audio.index', compact('audios'));
    }

    public function update(Request $request, string $part): RedirectResponse
    {
        abort_unless(in_array($part, ['A', 'B', 'C']), 404);
        abort_unless($request->user()->can('toefl.direction-audio.update'), 403);

        $request->validate([
            'file_path' => ['required', 'string', 'max:500'],
        ]);

        ToeflDirectionAudio::updateOrCreate(
            ['part' => $part],
            [
                'file_path' => $request->input('file_path'),
                'uploaded_by_employee_id' => $request->user()->employee->id,
            ]
        );

        return redirect()->route('toefl.direction-audio.index')->with('success', "Audio instruksi Part {$part} diperbarui.");
    }
}
